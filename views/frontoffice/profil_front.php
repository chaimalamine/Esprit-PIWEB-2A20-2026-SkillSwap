<?php
// Pas de session_start() car déjà fait dans frontdessign.php
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>

<!-- ========== CONTENU DU PROFIL (SANS SIDEBAR) ========== -->
<style>
    .profile-wrapper { max-width: 850px; margin: 25px auto; }
    .profile-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
    .profile-header { display: flex; align-items: center; gap: 20px; padding-bottom: 20px; border-bottom: 2px solid #f4f4f4; margin-bottom: 25px; }
    .avatar-pf { width: 75px; height: 75px; background: linear-gradient(135deg, #7b2ff7, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: bold; }
    .user-meta h2 { margin: 0 0 8px 0; color: #222; }
    .badge-pf { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #cd7f32; color: white; }
    .rep-score { color: #888; font-weight: 500; font-size: 14px; margin-top: 6px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
    .info-box { background: #f8f9fb; padding: 15px; border-radius: 10px; }
    .info-box label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; }
    .info-box span { font-size: 15px; color: #111; }
    .bio-box { background: #f8f9fb; padding: 15px; border-radius: 10px; margin-bottom: 25px; }
    .bio-box label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; }
    .bio-box p { margin: 0; color: #888; font-style: italic; }
    .actions { display: flex; gap: 15px; margin-top: 35px; padding-top: 25px; border-top: 2px solid #f4f4f4; }
    .btn-pf { padding: 12px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-edit-pf { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; }
    .btn-edit-pf:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(123, 47, 247, 0.3); }
    .btn-danger-pf { background: #fee2e2; color: #b91c1c; }
    .btn-danger-pf:hover { background: #fecaca; }
    .success-message { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
</style>

<div class="profile-wrapper">
    <div class="profile-card">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <div class="profile-header">
            <div class="avatar-pf"><?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1)); ?></div>
            <div class="user-meta">
                <h2><?php echo $_SESSION['user_prenom'] ?? 'Utilisateur'; ?> <?php echo $_SESSION['user_nom'] ?? ''; ?></h2>
                <span class="badge-pf">🏅 <?php echo $_SESSION['user_badge'] ?? 'Bronze'; ?></span>
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
            <a href="frontdessign.php?page=modifier" class="btn-pf btn-edit-pf" style="text-decoration: none; display: inline-block;">✏️ Modifier le profil</a>
            <a href="http://localhost/projetwebfinal/controllers/UserC.php?action=deleteProfile" class="btn-pf btn-danger-pf" style="text-decoration: none; display: inline-block;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">🗑️ Supprimer le compte</a>
        </div>
    </div>
</div>