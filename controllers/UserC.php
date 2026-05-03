<?php
// ============================================================
// ROUTAGE DES ACTIONS - Gère toutes les actions utilisateur
// Ce bloc s'exécute AVANT la classe quand une action est demandée
// ============================================================
if (isset($_GET['action'])) {
    
    // --- DÉCONNEXION ---
    if ($_GET['action'] == 'logout') {
        session_start();
        session_destroy();
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');
        exit();
    }
    
    // --- SUPPRESSION DU PROFIL ---
    if ($_GET['action'] == 'deleteProfile') {
        session_start();
        if (isset($_SESSION['user_id'])) {
            include_once __DIR__ . '/../config/config.php';
            $db = config::getConnexion();
            $req = $db->prepare('DELETE FROM utilisateur WHERE id_utilisateur = :id');
            $req->execute(array('id' => $_SESSION['user_id']));
            session_destroy();
        }
        header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesign.php');
        exit();
    }
    
    // ===========================================
    // MOT DE PASSE OUBLIE : Envoyer le lien par email
    // ===========================================
    if ($_GET['action'] == 'sendResetLink' && isset($_POST['email'])) {
        session_start();
        include_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../vendor/autoload.php';
        $db = config::getConnexion();
        
        $email = trim($_POST['email']);
        
        // Vérifier si l'email existe
        $req = $db->prepare("SELECT id_utilisateur, nom, prenom FROM utilisateur WHERE email = :email");
        $req->execute(array('email' => $email));
        $user = $req->fetch();
        
        if (!$user) {
            $_SESSION['erreur'] = 'Cet email n\'existe pas dans notre base';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
            exit();
        }
        
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Stocker le token dans la base
        $update = $db->prepare("UPDATE utilisateur SET reset_token = :token, reset_expires_at = :expires WHERE email = :email");
        $update->execute(array(
            'token' => $token,
            'expires' => $expiresAt,
            'email' => $email
        ));
        
        // Construire le lien de réinitialisation
        $resetLink = "http://localhost/projetwebfinal/controllers/UserC.php?action=showResetForm&token=" . $token;
        
        // Envoyer l'email avec PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eyettechouikhha@gmail.com'; // À remplacer par ton email
            $mail->Password = 'cmkn mqiq dmse gdmg'; // À remplacer par ton mot de passe d'application
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('eyettechouikhha@gmail.com', 'SkillSwap');
            $mail->addAddress($email, $user['prenom'] . ' ' . $user['nom']);
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe - SkillSwap';
            $mail->Body = '
                <h2>Bonjour ' . htmlspecialchars($user['prenom']) . ',</h2>
                <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                <p>Cliquez sur le lien ci-dessous (valable 15 minutes) :</p>
                <a href="' . $resetLink . '">' . $resetLink . '</a>
                <p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.</p>
                <p>Cordialement,<br>L\'équipe SkillSwap</p>
            ';
            $mail->send();
            
            $_SESSION['reset_sent'] = 'Un email avec un lien de réinitialisation a été envoyé à ' . $email;
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
        } catch (Exception $e) {
            $_SESSION['erreur'] = "L'envoi de l'email a échoué : " . $mail->ErrorInfo;
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
        }
        exit();
    }
    
    // ===========================================
    // MOT DE PASSE OUBLIE : Afficher le formulaire de réinitialisation
    // ===========================================
    if ($_GET['action'] == 'showResetForm' && isset($_GET['token'])) {
        session_start();
        include_once __DIR__ . '/../config/config.php';
        $db = config::getConnexion();
        
        $token = $_GET['token'];
        
        // Vérifier si le token existe et n'a pas expiré
        $req = $db->prepare("SELECT id_utilisateur, email FROM utilisateur WHERE reset_token = :token AND reset_expires_at > NOW()");
        $req->execute(array('token' => $token));
        $user = $req->fetch();
        
        if (!$user) {
            $_SESSION['erreur'] = 'Lien invalide ou expiré. Veuillez recommencer.';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
            exit();
        }
        
        // Stocker le token en session
        $_SESSION['reset_token'] = $token;
        
        // Rediriger vers la vue du formulaire
        header('Location: http://localhost/projetwebfinal/views/frontoffice/reinitialiser_mot_de_passe.php');
        exit();
    }
    
    // ===========================================
    // MOT DE PASSE OUBLIE : Mettre à jour le mot de passe
    // ===========================================
    if ($_GET['action'] == 'updatePasswordWithToken' && isset($_POST['mot_de_passe'])) {
        session_start();
        include_once __DIR__ . '/../config/config.php';
        $db = config::getConnexion();
        
        // Vérifier qu'un token est stocké en session
        if (!isset($_SESSION['reset_token'])) {
            $_SESSION['erreur'] = 'Session expirée, veuillez recommencer';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
            exit();
        }
        
        $token = $_SESSION['reset_token'];
        $newPassword = $_POST['mot_de_passe'];
        $confirmPassword = $_POST['confirme_mot_de_passe'];
        
        // Vérifier que les mots de passe correspondent
        if ($newPassword !== $confirmPassword) {
            $_SESSION['erreur'] = 'Les mots de passe ne correspondent pas';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/reinitialiser_mot_de_passe.php');
            exit();
        }
        
        if (empty($newPassword)) {
            $_SESSION['erreur'] = 'Le mot de passe ne peut pas être vide';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/reinitialiser_mot_de_passe.php');
            exit();
        }
        
        // Vérifier que le token est encore valide
        $req = $db->prepare("SELECT email FROM utilisateur WHERE reset_token = :token AND reset_expires_at > NOW()");
        $req->execute(array('token' => $token));
        $user = $req->fetch();
        
        if (!$user) {
            $_SESSION['erreur'] = 'Lien expiré. Veuillez recommencer.';
            unset($_SESSION['reset_token']);
            header('Location: http://localhost/projetwebfinal/views/frontoffice/mot_de_passe_oublie.php');
            exit();
        }
        
        // Hacher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Mettre à jour dans la base et supprimer le token
        $update = $db->prepare("UPDATE utilisateur SET mot_de_passe = :mdp, reset_token = NULL, reset_expires_at = NULL WHERE reset_token = :token");
        $update->execute(array(
            'mdp' => $hashedPassword,
            'token' => $token
        ));
        
        // Nettoyer la session
        unset($_SESSION['reset_token']);
        $_SESSION['reset_success'] = 'Mot de passe modifié avec succès ! Connectez-vous.';
        header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
        exit();
    }

    // ===========================================
    // CONNEXION
    // ===========================================
    if ($_GET['action'] == 'login' && isset($_POST['email'])) {
        session_start();
        include_once __DIR__ . '/../config/config.php';
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
            $_SESSION['user_role'] = $user['role'] ?? 'utilisateur';
            $_SESSION['user_score'] = $user['score_reputation'];
            $_SESSION['user_badge'] = $user['badge_confiance'];
            $_SESSION['user_bio'] = $user['bio'] ?? '';
            $_SESSION['user_date_inscription'] = date('d/m/Y', strtotime($user['date_inscription']));
            
            header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php');
            exit();
        } else {
            $_SESSION['erreur'] = 'Email ou mot de passe incorrect';
            header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
            exit();
        }
    }
    
    // ===========================================
    // INSCRIPTION
    // ===========================================
    if ($_GET['action'] == 'register' && isset($_POST['nom'])) {
        session_start();
        include_once __DIR__ . '/../config/config.php';
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
        
        $role = isset($_POST['role']) ? $_POST['role'] : 'utilisateur';
        if (!in_array($role, array('utilisateur', 'admin'))) {
            $role = 'utilisateur';
        }
        
        $req = $db->prepare('
            INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut, role, score_reputation, badge_confiance) 
            VALUES (:nom, :prenom, :email, :mdp, :statut, :role, :score, :badge)
        ');
        $req->execute(array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mdp' => password_hash($mot_de_passe, PASSWORD_DEFAULT),
            'statut' => 'Actif',
            'role' => $role,
            'score' => 0,
            'badge' => 'Debutant'
        ));
        
        $_SESSION['success'] = 'Inscription réussie !';
        header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
        exit();
    }
    
    // ===========================================
    // MODIFICATION DU PROFIL
    // ===========================================
    if ($_GET['action'] == 'updateProfile' && isset($_POST['nom'])) {
        session_start();
        if (isset($_SESSION['user_id'])) {
            include_once __DIR__ . '/../config/config.php';
            $db = config::getConnexion();
            
            $id = $_SESSION['user_id'];
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
            
            if (empty($nom) || empty($prenom) || empty($email)) {
                $_SESSION['erreur'] = 'Le nom, prénom et email sont obligatoires';
                if (isset($_POST['from_front']) && $_POST['from_front'] == '1') {
                    header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php?page=modifier');
                } else {
                    header('Location: http://localhost/projetwebfinal/views/backoffice/modifier_profil.php');
                }
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['erreur'] = 'Format d\'email invalide';
                if (isset($_POST['from_front']) && $_POST['from_front'] == '1') {
                    header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdessign.php?page=modifier');
                } else {
                    header('Location: http://localhost/projetwebfinal/views/backoffice/modifier_profil.php');
                }
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
include_once __DIR__ . '/../config/config.php';
include_once __DIR__ . '/../models/User.php';

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
    
    function updateUser($user, $id) {
        $db = config::getConnexion();
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
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'statut' => $user->getStatut(),
            'role' => $user->getRole(),
            'score' => $user->getScore_reputation(),
            'badge' => $user->getBadge_confiance()
        ));
    }
}