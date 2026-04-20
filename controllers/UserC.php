<?php
// --- ROUTAGE DES ACTIONS (TOUT EN HAUT, SANS RIEN AVANT) ---
if (isset($_GET['action'])) {
    
    if ($_GET['action'] == 'logout') {
        session_start();
        session_destroy();
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');
        exit();
    }
    
    if ($_GET['action'] == 'deleteProfile') {
        session_start();
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/../config/config.php';
            $db = config::getConnexion();
            $req = $db->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');
            $req->execute(array('id' => $_SESSION['user_id']));
            session_destroy();
        }
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');
        exit();
    }
    
    // --- CONNEXION AVEC VALIDATION ---
    if ($_GET['action'] == 'login' && isset($_POST['email'])) {
        session_start();
        include __DIR__ . '/../config/config.php';
        $db = config::getConnexion();
        
        $email = trim($_POST['email']);
        $mot_de_passe = $_POST['mot_de_passe'];
        
        if (empty($email) || empty($mot_de_passe)) {
            $_SESSION['erreur'] = 'Email et mot de passe obligatoires';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
            exit();
        }
        
        $req = $db->prepare("SELECT * FROM utilisateur WHERE email = :email AND statut = 'Actif'");
        $req->execute(array('email' => $email));
        $user = $req->fetch();
        
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_statut'] = $user['statut'];
            $_SESSION['user_score'] = $user['score_reputation'];
            $_SESSION['user_badge'] = $user['badge_confiance'];
            $_SESSION['user_bio'] = $user['bio'] ?? '';
            $_SESSION['user_date_inscription'] = date('d/m/Y', strtotime($user['date_inscription']));
            
            header('Location: http://localhost/projetwebfinal/views/backoffice/design.php');
            exit();
        } else {
            $_SESSION['erreur'] = 'Email ou mot de passe incorrect';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
            exit();
        }
    }
    
    // --- INSCRIPTION AVEC VALIDATION ---
    if ($_GET['action'] == 'register' && isset($_POST['nom'])) {
        session_start();
        include __DIR__ . '/../config/config.php';
        $db = config::getConnexion();
        
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $mot_de_passe = $_POST['mot_de_passe'];
        $confirm = $_POST['confirm_mot_de_passe'];
        
        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
            $_SESSION['erreur'] = 'Tous les champs sont obligatoires';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        if ($mot_de_passe != $confirm) {
            $_SESSION['erreur'] = 'Les mots de passe ne correspondent pas';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erreur'] = 'Format d\'email invalide';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        $check = $db->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
        $check->execute(array('email' => $email));
        if ($check->rowCount() > 0) {
            $_SESSION['erreur'] = 'Cet email est déjà utilisé';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        $req = $db->prepare('
            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut, score_reputation, badge_confiance) 
            VALUES (:nom, :prenom, :email, :mdp, :statut, :score, :badge)
        ');
        $req->execute(array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mdp' => password_hash($mot_de_passe, PASSWORD_DEFAULT),
            'statut' => 'Actif',
            'score' => 0,
            'badge' => 'Debutant'
        ));
        
        $_SESSION['success'] = 'Inscription réussie !';
        header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
        exit();
    }
    
    // --- MODIFICATION PROFIL AVEC VALIDATION ---
    if ($_GET['action'] == 'updateProfile' && isset($_POST['nom'])) {
        session_start();
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/../config/config.php';
            $db = config::getConnexion();
            
            $id = $_SESSION['user_id'];
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
            
            if (empty($nom) || empty($prenom) || empty($email)) {
                $_SESSION['erreur'] = 'Le nom, prénom et email sont obligatoires';
                header('Location: http://localhost/projetwebfinal/views/backoffice/profil.php');
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['erreur'] = 'Format d\'email invalide';
                header('Location: http://localhost/projetwebfinal/views/backoffice/profil.php');
                exit();
            }
            
            $req = $db->prepare('
                UPDATE utilisateur 
                SET nom = :nom, prenom = :prenom, email = :email, bio = :bio
                WHERE id_utilisateur = :id
            ');
            $req->execute(array(
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'bio' => $bio
            ));
            
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_bio'] = $bio;
            $_SESSION['success'] = 'Profil mis à jour !';
        }
        header('Location: http://localhost/projetwebfinal/views/backoffice/profil.php');
        exit();
    }
}

// --- CLASSE USERC ---
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../models/User.php';

class UserC {

    function listeUser() {
        $db = config::getConnexion();
        $liste = $db->query('SELECT * FROM utilisateur ORDER BY nom ASC');
        return $liste;
    }

    function deleteUser($id) {
        $db = config::getConnexion();
        $req = $db->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');
        $req->execute(array('id' => $id));
    }

    function updateUser($user, $id) {
        $db = config::getConnexion();
        $req = $db->prepare('
            UPDATE utilisateur 
            SET nom = :nom, 
                prenom = :prenom, 
                email = :email, 
                statut = :statut,
                score_reputation = :score,
                badge_confiance = :badge
            WHERE id_utilisateur = :id
        ');
        $req->execute(array(
            'id' => $id,
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'statut' => $user->getStatut(),
            'score' => $user->getScore_reputation(),
            'badge' => $user->getBadge_confiance()
        ));
    }

    function searchUser($search) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT * FROM utilisateur 
            WHERE nom LIKE :search OR prenom LIKE :search OR email LIKE :search
        ');
        $req->execute(array('search' => '%' . $search . '%'));
        return $req->fetchAll();
    }

    function sortUser($tri) {
        $db = config::getConnexion();
        $allowed = array('nom', 'prenom', 'date_inscription', 'score_reputation');
        if (!in_array($tri, $allowed)) {
            $tri = 'nom';
        }
        $req = $db->query('SELECT * FROM utilisateur ORDER BY ' . $tri . ' ASC');
        return $req->fetchAll();
    }

}
?>