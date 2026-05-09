<?php
/**
 * Contrôleur Parrainage - SkillSwap
 * Gère toutes les actions liées au système de parrainage
 */

include_once __DIR__ . '/config/config.php';
include_once __DIR__ . '/../models/Parrainage.php';

// ============================================================
// ROUTAGE DES ACTIONS
// ============================================================
if (isset($_GET['action'])) {
    session_start();

    // --- ENVOYER UNE INVITATION ---
    if ($_GET['action'] == 'inviter' && isset($_POST['email_invite'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/frontoffice/connexion.php');
            exit();
        }

        include_once __DIR__ . '/config/config.php';
        $db = config::getConnexion();

        $email_invite = trim($_POST['email_invite']);
        $id_parrain   = $_SESSION['user_id'];

        // Validation email
        if (!filter_var($email_invite, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erreur_parrainage'] = 'Adresse email invalide.';
            header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
            exit();
        }

        // L'utilisateur ne peut pas s'inviter lui-même
        if ($email_invite === $_SESSION['user_email']) {
            $_SESSION['erreur_parrainage'] = 'Vous ne pouvez pas vous parrainer vous-même.';
            header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
            exit();
        }

        // Vérifier si une invitation existe déjà pour cet email par ce parrain
        $check = $db->prepare("SELECT id_parrainage FROM parrainage WHERE id_parrain = :parrain AND email_invite = :email AND statut != 'expire'");
        $check->execute(['parrain' => $id_parrain, 'email' => $email_invite]);
        if ($check->rowCount() > 0) {
            $_SESSION['erreur_parrainage'] = 'Vous avez déjà envoyé une invitation à cet email.';
            header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
            exit();
        }

        // Vérifier si l'email est déjà inscrit
        $checkUser = $db->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = :email");
        $checkUser->execute(['email' => $email_invite]);
        if ($checkUser->rowCount() > 0) {
            $_SESSION['erreur_parrainage'] = 'Cet utilisateur est déjà inscrit sur SkillSwap.';
            header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
            exit();
        }

        // Générer un code unique
        $code = strtoupper(substr(md5(uniqid($email_invite . $id_parrain, true)), 0, 10));

        // Insérer l'invitation
        $req = $db->prepare("
            INSERT INTO parrainage (id_parrain, email_invite, code_parrainage, statut, date_invitation)
            VALUES (:parrain, :email, :code, 'en_attente', NOW())
        ");
        $req->execute([
            'parrain' => $id_parrain,
            'email'   => $email_invite,
            'code'    => $code
        ]);

        $_SESSION['success_parrainage'] = "Invitation envoyée à $email_invite ! Code : $code";
        header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
        exit();
    }

    // --- ANNULER UNE INVITATION (parrain) ---
    if ($_GET['action'] == 'annuler' && isset($_GET['id'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/frontoffice/connexion.php');
            exit();
        }

        include_once __DIR__ . '/config/config.php';
        $db = config::getConnexion();

        $id = (int)$_GET['id'];
        // S'assurer que l'invitation appartient bien au parrain connecté
        $req = $db->prepare("UPDATE parrainage SET statut = 'expire' WHERE id_parrainage = :id AND id_parrain = :parrain");
        $req->execute(['id' => $id, 'parrain' => $_SESSION['user_id']]);

        $_SESSION['success_parrainage'] = 'Invitation annulée.';
        header('Location: ../views/frontoffice/frontdessign.php?page=parrainage');
        exit();
    }

    // --- ACCEPTER UN PARRAINAGE (lors de l'inscription avec code) ---
    if ($_GET['action'] == 'accepter' && isset($_POST['code_parrainage'])) {
        include_once __DIR__ . '/config/config.php';
        $db = config::getConnexion();

        $code      = trim($_POST['code_parrainage']);
        $id_filleul = isset($_POST['id_filleul']) ? (int)$_POST['id_filleul'] : 0;

        if ($code === '' || $id_filleul === 0) {
            header('Location: ../views/frontoffice/connexion.php');
            exit();
        }

        // Vérifier que le code est valide et en attente
        $req = $db->prepare("SELECT * FROM parrainage WHERE code_parrainage = :code AND statut = 'en_attente'");
        $req->execute(['code' => $code]);
        $parrainage = $req->fetch();

        if ($parrainage) {
            // Mettre à jour le parrainage
            $upd = $db->prepare("
                UPDATE parrainage
                SET statut = 'accepte', id_filleul = :filleul, date_acceptation = NOW()
                WHERE id_parrainage = :id
            ");
            $upd->execute(['filleul' => $id_filleul, 'id' => $parrainage['id_parrainage']]);

            // Récompenser le parrain (+10 points)
            $db->prepare("UPDATE utilisateur SET score_reputation = score_reputation + 10 WHERE id_utilisateur = :id")
               ->execute(['id' => $parrainage['id_parrain']]);

            // Récompenser le filleul (+5 points)
            $db->prepare("UPDATE utilisateur SET score_reputation = score_reputation + 5 WHERE id_utilisateur = :id")
               ->execute(['id' => $id_filleul]);
        }

        header('Location: ../views/frontoffice/connexion.php');
        exit();
    }

    // --- SUPPRIMER UN PARRAINAGE (admin) ---
    if ($_GET['action'] == 'supprimer_admin' && isset($_GET['id'])) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ../views/frontoffice/frontdessign.php');
            exit();
        }

        include_once __DIR__ . '/config/config.php';
        $db = config::getConnexion();

        $id = (int)$_GET['id'];
        $db->prepare("DELETE FROM parrainage WHERE id_parrainage = :id")->execute(['id' => $id]);

        $_SESSION['success_parrainage'] = 'Parrainage supprimé.';
        header('Location: ../views/backoffice/liste_parrainages.php');
        exit();
    }
}

// ============================================================
// CLASSE PARRAINAGEC
// ============================================================
class ParrainageC {

    // Récupérer toutes les invitations envoyées par un parrain
    public function getInvitationsByParrain($id_parrain) {
        $db = config::getConnexion();
        $req = $db->prepare("
            SELECT p.*, 
                   u.nom AS nom_filleul, u.prenom AS prenom_filleul
            FROM parrainage p
            LEFT JOIN utilisateur u ON p.id_filleul = u.id_utilisateur
            WHERE p.id_parrain = :id
            ORDER BY p.date_invitation DESC
        ");
        $req->execute(['id' => $id_parrain]);
        return $req->fetchAll();
    }

    // Récupérer les stats d'un parrain
    public function getStatsByParrain($id_parrain) {
        $db = config::getConnexion();
        $req = $db->prepare("
            SELECT 
                COUNT(*) AS total_invitations,
                SUM(CASE WHEN statut = 'accepte' THEN 1 ELSE 0 END) AS acceptees,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS en_attente,
                SUM(CASE WHEN statut = 'expire' THEN 1 ELSE 0 END) AS expirees
            FROM parrainage
            WHERE id_parrain = :id
        ");
        $req->execute(['id' => $id_parrain]);
        return $req->fetch();
    }

    // Récupérer tous les parrainages (admin)
    public function getAllParrainages() {
        $db = config::getConnexion();
        $req = $db->query("
            SELECT p.*,
                   parrain.nom AS nom_parrain, parrain.prenom AS prenom_parrain,
                   filleul.nom AS nom_filleul, filleul.prenom AS prenom_filleul
            FROM parrainage p
            JOIN utilisateur parrain ON p.id_parrain = parrain.id_utilisateur
            LEFT JOIN utilisateur filleul ON p.id_filleul = filleul.id_utilisateur
            ORDER BY p.date_invitation DESC
        ");
        return $req->fetchAll();
    }

    // Statistiques globales (admin)
    public function getStatsGlobales() {
        $db = config::getConnexion();
        $req = $db->query("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN statut = 'accepte' THEN 1 ELSE 0 END) AS acceptees,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS en_attente,
                SUM(CASE WHEN statut = 'expire' THEN 1 ELSE 0 END) AS expirees
            FROM parrainage
        ");
        return $req->fetch();
    }

    // Vérifier si un code de parrainage est valide
    public function verifierCode($code) {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT * FROM parrainage WHERE code_parrainage = :code AND statut = 'en_attente'");
        $req->execute(['code' => $code]);
        return $req->fetch();
    }
}
?>
