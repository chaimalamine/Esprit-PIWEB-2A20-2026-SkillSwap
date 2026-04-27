<?php
// ============================================================
// ROUTAGE DES ACTIONS - Gère toutes les actions utilisateur
// Ce bloc s'exécute AVANT la classe quand une action est demandée
// ============================================================
if (isset($_GET['action'])) {
    
    // --- DÉCONNEXION ---
    if ($_GET['action'] == 'logout') {
        session_start();                          // Démarrer la session
        session_destroy();                        // Détruire toutes les données de session
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');  // Rediriger vers l'accueil
        exit();                                   // Arrêter l'exécution du script
    }
    
    // --- SUPPRESSION DU PROFIL ---
    if ($_GET['action'] == 'deleteProfile') {
        session_start();                          // Démarrer la session
        if (isset($_SESSION['user_id'])) {        // Vérifier que l'utilisateur est connecté
            include_once __DIR__ . '/../config/config.php';  // Inclure la config (connexion BDD)
            $db = config::getConnexion();         // Obtenir la connexion PDO
            $req = $db->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');  // Préparer la suppression
            $req->execute(array('id' => $_SESSION['user_id']));  // Exécuter avec l'ID connecté
            session_destroy();                    // Détruire la session après suppression
        }
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');  // Rediriger vers l'accueil
        exit();                                   // Arrêter l'exécution
    }
    
    // ===========================================
    // CONNEXION - Vérifie email et mot de passe
    // ===========================================
    if ($_GET['action'] == 'login' && isset($_POST['email'])) {
        session_start();                          // Démarrer la session
        include_once __DIR__ . '/../config/config.php';  // Inclure la config
        $db = config::getConnexion();             // Obtenir la connexion PDO
        
        $email = trim($_POST['email']);           // Récupérer et nettoyer l'email
        $mot_de_passe = $_POST['mot_de_passe'];   // Récupérer le mot de passe
        
        // Vérifier que les champs ne sont pas vides
        if (empty($email) || empty($mot_de_passe)) {
            $_SESSION['erreur'] = 'Email et mot de passe obligatoires';  // Message d'erreur
            header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');  // Rediriger
            exit();                               // Arrêter l'exécution
        }
        
        // Rechercher l'utilisateur dans la base
        $req = $db->prepare("SELECT * FROM utilisateur WHERE email = :email AND statut = 'Actif'");
        $req->execute(array('email' => $email));   // Exécuter avec l'email
        $user = $req->fetch();                    // Récupérer l'utilisateur
        
        // Vérifier l'utilisateur existe ET que le mot de passe correspond
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // --- STOCKER TOUTES LES INFOS DANS LA SESSION ---
            $_SESSION['user_id'] = $user['id_utilisateur'];       // ID unique
            $_SESSION['user_nom'] = $user['nom'];                  // Nom
            $_SESSION['user_prenom'] = $user['prenom'];            // Prénom
            $_SESSION['user_email'] = $user['email'];              // Email
            $_SESSION['user_statut'] = $user['statut'];            // Statut (Actif/Inactif)
            $_SESSION['user_role'] = $user['role'] ?? 'utilisateur';  // Rôle (admin/utilisateur)
            $_SESSION['user_score'] = $user['score_reputation'];   // Score de réputation
            $_SESSION['user_badge'] = $user['badge_confiance'];    // Badge de confiance
            $_SESSION['user_bio'] = $user['bio'] ?? '';            // Bio (peut être vide)
            $_SESSION['user_date_inscription'] = date('d/m/Y', strtotime($user['date_inscription']));  // Date formatée
            
            header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php');  // Rediriger vers l'accueil connecté
            exit();                               // Arrêter l'exécution
        } else {
            $_SESSION['erreur'] = 'Email ou mot de passe incorrect';  // Message d'erreur
            header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');  // Rediriger vers connexion
            exit();                               // Arrêter l'exécution
        }
    }
    
    // ===========================================
    // INSCRIPTION - Créer un nouveau compte
    // ===========================================
    if ($_GET['action'] == 'register' && isset($_POST['nom'])) {
        session_start();                          // Démarrer la session
        include_once __DIR__ . '/../config/config.php';  // Inclure la config
        $db = config::getConnexion();             // Obtenir la connexion PDO
        
        // Récupérer tous les champs du formulaire
        $nom = trim($_POST['nom']);               // Nettoyer le nom
        $prenom = trim($_POST['prenom']);         // Nettoyer le prénom
        $email = trim($_POST['email']);           // Nettoyer l'email
        $mot_de_passe = $_POST['mot_de_passe'];   // Mot de passe
        $confirm = $_POST['confirm_mot_de_passe']; // Confirmation du mot de passe
        
        // VÉRIFICATION 1 : Tous les champs sont remplis
        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
            $_SESSION['erreur'] = 'Tous les champs sont obligatoires';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        // VÉRIFICATION 2 : Les mots de passe correspondent
        if ($mot_de_passe != $confirm) {
            $_SESSION['erreur'] = 'Les mots de passe ne correspondent pas';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        // VÉRIFICATION 3 : Format d'email valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erreur'] = 'Format d\'email invalide';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        // VÉRIFICATION 4 : L'email n'est pas déjà utilisé
        $check = $db->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email');
        $check->execute(array('email' => $email));
        if ($check->rowCount() > 0) {
            $_SESSION['erreur'] = 'Cet email est déjà utilisé';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/inscription.php');
            exit();
        }
        
        // VÉRIFICATION 5 : Validation du rôle
        $role = isset($_POST['role']) ? $_POST['role'] : 'utilisateur';  // Récupérer le rôle choisi
        if (!in_array($role, array('utilisateur', 'admin'))) {           // Vérifier que le rôle est valide
            $role = 'utilisateur';                                        // Mettre utilisateur par défaut
        }
        
        // INSÉRER le nouvel utilisateur dans la base de données
        $req = $db->prepare('
            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut, role, score_reputation, badge_confiance) 
            VALUES (:nom, :prenom, :email, :mdp, :statut, :role, :score, :badge)
        ');
        $req->execute(array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mdp' => password_hash($mot_de_passe, PASSWORD_DEFAULT),  // Hasher le mot de passe (sécurité)
            'statut' => 'Actif',         // Nouveau compte = Actif par défaut
            'role' => $role,              // Rôle choisi (utilisateur ou admin)
            'score' => 0,                 // Score initial = 0
            'badge' => 'Debutant'         // Badge initial = Débutant
        ));
        
        $_SESSION['success'] = 'Inscription réussie !';  // Message de succès
        header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');  // Rediriger vers connexion
        exit();
    }
    
    // ===========================================
    // MODIFICATION DU PROFIL
    // ===========================================
    if ($_GET['action'] == 'updateProfile' && isset($_POST['nom'])) {
        session_start();                          // Démarrer la session
        if (isset($_SESSION['user_id'])) {        // Vérifier que l'utilisateur est connecté
            include_once __DIR__ . '/../config/config.php';  // Inclure la config
            $db = config::getConnexion();         // Obtenir la connexion PDO
            
            $id = $_SESSION['user_id'];            // ID de l'utilisateur connecté
            $nom = trim($_POST['nom']);            // Nouveau nom
            $prenom = trim($_POST['prenom']);      // Nouveau prénom
            $email = trim($_POST['email']);         // Nouvel email
            $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';  // Bio (optionnelle)
            
            // VÉRIFICATION : Champs obligatoires
            if (empty($nom) || empty($prenom) || empty($email)) {
                $_SESSION['erreur'] = 'Le nom, prénom et email sont obligatoires';
                // Rediriger selon l'origine (frontoffice ou backoffice)
                if (isset($_POST['from_front']) && $_POST['from_front'] == '1') {
                    header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php?page=modifier');
                } else {
                    header('Location: http://localhost/projetwebfinal/views/backoffice/modifier_profil.php');
                }
                exit();
            }
            
            // VÉRIFICATION : Format email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['erreur'] = 'Format d\'email invalide';
                if (isset($_POST['from_front']) && $_POST['from_front'] == '1') {
                    header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php?page=modifier');
                } else {
                    header('Location: http://localhost/projetwebfinal/views/backoffice/modifier_profil.php');
                }
                exit();
            }
            
            // METTRE À JOUR dans la base de données
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
            
            // METTRE À JOUR les variables de session
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_bio'] = $bio;
            $_SESSION['success'] = 'Profil mis à jour !';  // Message de succès
        }
        // Rediriger selon l'origine
        if (isset($_POST['from_front']) && $_POST['from_front'] == '1') {
            header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php?page=profil');
        } else {
            header('Location: http://localhost/projetwebfinal/views/backoffice/profil.php');
        }
        exit();
    }
}

// ============================================================
// CLASSE USERC - Contrôleur des opérations utilisateur
// ============================================================
include_once __DIR__ . '/../config/config.php';  // Inclure la config (connexion BDD)
include_once __DIR__ . '/../models/User.php';    // Inclure le modèle User (classe)

class UserC {

    // --- LISTER tous les utilisateurs (ordre alphabétique) ---
    function listeUser() {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $liste = $db->query('SELECT * FROM utilisateur ORDER BY nom ASC');  // Requête SQL
        return $liste;                            // Retourner les résultats
    }

    // --- SUPPRIMER un utilisateur par son ID ---
    function deleteUser($id) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');  // Préparer la suppression
        $req->execute(array('id' => $id));        // Exécuter avec l'ID
    }

    // --- RECHERCHER des utilisateurs par nom, prénom ou email ---
    function searchUser($search) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            SELECT * FROM utilisateur 
            WHERE nom LIKE :search OR prenom LIKE :search OR email LIKE :search
        ');
        $req->execute(array('search' => '%' . $search . '%'));  // %search% = contient le mot recherché
        return $req->fetchAll();                  // Retourner tous les résultats
    }

    // --- TRIER les utilisateurs par un champ spécifique ---
    function sortUser($tri) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $allowed = array('nom', 'prenom', 'date_inscription', 'score_reputation');  // Colonnes autorisées
        if (!in_array($tri, $allowed)) {          // Si le champ n'est pas autorisé
            $tri = 'nom';                         // Trier par nom par défaut
        }
        $req = $db->query('SELECT * FROM utilisateur ORDER BY ' . $tri . ' ASC');  // Requête avec tri
        return $req->fetchAll();                  // Retourner tous les résultats
    }
    
    // --- MODIFIER un utilisateur (admin) ---
    function updateUser($user, $id) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            UPDATE utilisateur 
            SET nom = :nom,                       
                prenom = :prenom,                 
                email = :email,                   
                statut = :statut,                 
                role = :role,                     
                score_reputation = :score,       
                badge_confiance = :badge          
            WHERE id_utilisateur = :id           
        ');
        $req->execute(array(
            'id' => $id,
            'nom' => $user->getNom(),             // Récupérer le nom via le getter
            'prenom' => $user->getPrenom(),       // Récupérer le prénom via le getter
            'email' => $user->getEmail(),         // Récupérer l'email via le getter
            'statut' => $user->getStatut(),       // Récupérer le statut via le getter
            'role' => $user->getRole(),           // Récupérer le rôle via le getter
            'score' => $user->getScore_reputation(),  // Récupérer le score via le getter
            'badge' => $user->getBadge_confiance()    // Récupérer le badge via le getter
        ));
    }

}
?>