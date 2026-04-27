<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// --- DÉTECTER SI INCLUS DANS FRONTDESIGN ---
$inclus_dans_frontdesign = defined('INCLUS_FRONT');
?>
<?php if(!$inclus_dans_frontdesign): // Afficher la structure complète seulement si pas inclus ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - SkillSwap</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: #eef2f7; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; padding: 20px; }
        .topbar { background: white; padding: 15px 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; }
</style>
</head>
<body>
    <div class="sidebar">
        <h2>SkillSwap</h2>
        <a href="design.php">Dashboard</a>
        <a href="liste_users.php">Liste des utilisateurs</a>
        <a href="mes_competences.php">Mes Compétences</a>
        <a href="#">Offres</a>
        <a href="#">Messages</a>
        <a href="profil.php" style="background: rgba(255,255,255,0.2);">Profil</a>
        <a href="../frontoffice/frontdessign.php" style="margin-top: 30px; background: rgba(255,255,255,0.1);">← Retour au site</a>
    </div>
    <div class="main">
        <div class="topbar">
            <h3>Gestion du Profil</h3>
            <a href="http://localhost/projetwebfinal/controllers/logout.php" style="background: #ef4444; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px;">Déconnexion</a>
        </div>
<?php endif; ?>

        <!-- ========== CONTENU DU PROFIL (AFFICHÉ DANS LES DEUX CAS) ========== -->
        <style>
            .profile-wrapper { max-width: 850px; margin: 25px auto; }
            .profile-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
            .profile-header { display: flex; align-items: center; gap: 20px; padding-bottom: 20px; border-bottom: 2px solid #f4f4f4; margin-bottom: 25px; }
            .avatar { width: 75px; height: 75px; background: linear-gradient(135deg, #7b2ff7, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: bold; }
            .user-meta h2 { margin: 0 0 8px 0; color: #222; }
            .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #cd7f32; color: white; }
            .rep-score { color: #888; font-weight: 500; font-size: 14px; margin-top: 6px; }
            .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
            .info-box { background: #f8f9fb; padding: 15px; border-radius: 10px; }
            .info-box label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; }
            .info-box span { font-size: 15px; color: #111; }
            .bio-box { background: #f8f9fb; padding: 15px; border-radius: 10px; margin-bottom: 25px; }
            .bio-box label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; }
            .bio-box p { margin: 0; color: #888; font-style: italic; }
            .actions { display: flex; gap: 15px; margin-top: 35px; padding-top: 25px; border-top: 2px solid #f4f4f4; }
            .btn { padding: 12px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; }
            .btn-edit { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; }
            .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(123, 47, 247, 0.3); }
            .btn-danger { background: #fee2e2; color: #b91c1c; }
            .btn-danger:hover { background: #fecaca; }
            .success-message { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
        </style>

        <div class="profile-wrapper">
            <div class="profile-card">
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <div class="profile-header">
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1)); ?></div>
                    <div class="user-meta">
                        <h2><?php echo $_SESSION['user_prenom'] ?? 'Utilisateur'; ?> <?php echo $_SESSION['user_nom'] ?? ''; ?></h2>
                        <span class="badge">🥉 <?php echo $_SESSION['user_badge'] ?? 'Bronze'; ?></span>
                        <div class="rep-score">⭐ <?php echo $_SESSION['user_score'] ?? '0'; ?> / 5.0 (0 avis)</div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-box">
                        <label>Email</label>
                        <span><?php echo $_SESSION['user_email'] ?? ''; ?></span>
                    </div>
                    <div class="info-box">
                        <label>Membre depuis</label>
                        <span><?php echo $_SESSION['user_date_inscription'] ?? date('d/m/Y'); ?></span>
                    </div>
                </div>

                <div class="bio-box">
                    <label>Bio / Présentation</label>
                    <p><?php echo !empty($_SESSION['user_bio']) ? $_SESSION['user_bio'] : 'Aucune bio définie pour le moment.'; ?></p>
                </div>

                <div class="actions">
                    <?php if($inclus_dans_frontdesign): ?>
                        <a href="frontdesign.php?page=modifier" class="btn btn-edit" style="text-decoration: none; display: inline-block;">✏️ Modifier le profil</a>
                    <?php else: ?>
                        <a href="modifier_profil.php" class="btn btn-edit" style="text-decoration: none; display: inline-block;">✏️ Modifier le profil</a>
                    <?php endif; ?>
                    <a href="http://localhost/projetwebfinal/controllers/UserC.php?action=deleteProfile" class="btn btn-danger" style="text-decoration: none; display: inline-block;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">🗑️ Supprimer le compte</a>
                </div>
            </div>
        </div>

<?php if(!$inclus_dans_frontdesign): // Fermer la structure complète ?>
    </div>
</body>
</html>
<?php endif; ?>