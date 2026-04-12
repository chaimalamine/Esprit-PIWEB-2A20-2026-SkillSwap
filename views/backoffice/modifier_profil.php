<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil - SkillSwap</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: #eef2f7; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; padding: 20px; }
        .topbar { background: white; padding: 15px 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; }
        .profile-wrapper { max-width: 850px; margin: 25px auto; }
        .profile-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 14px; background: #fafafa; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #7b2ff7; background: white; box-shadow: 0 0 0 4px rgba(123, 47, 247, 0.1); }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
        .btn-group { display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #f4f4f4; }
        .btn { padding: 12px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-save { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(123, 47, 247, 0.3); }
        .btn-cancel { background: #f3f4f6; color: #4b5563; }
        .btn-cancel:hover { background: #e5e7eb; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SkillSwap</h2>
        <a href="design.php">Dashboard</a>
        <a href="liste_users.php">Liste des utilisateurs</a>
        <a href="#">Mes Compétences</a>
        <a href="#">Offres</a>
        <a href="#">Messages</a>
        <a href="profil.php">Profil</a>
        <a href="../../views/frontoffice/frontdessign.php" style="margin-top: 30px; background: rgba(255,255,255,0.1);">← Retour au site</a>
    </div>

    <div class="main">
        <div class="topbar">
            <h3>Modifier le Profil</h3>
            <a href="http://localhost/projetwebfinal/controllers/UserC.php?action=logout" style="background: #ef4444; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px;">Déconnexion</a>
        </div>

        <div class="profile-wrapper">
            <div class="profile-card">
                <?php if(isset($_SESSION['erreur'])): ?>
                    <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
                <?php endif; ?>

                <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=updateProfile" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" placeholder="Ton nom" value="<?php echo $_SESSION['user_nom'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" placeholder="Ton prénom" value="<?php echo $_SESSION['user_prenom'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" placeholder="exemple@email.com" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Bio / Présentation</label>
                        <textarea name="bio" placeholder="Décris-toi en quelques lignes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Compétences (séparées par des virgules)</label>
                        <input type="text" name="competences" placeholder="Ex: HTML, CSS, Design, Montage vidéo">
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-save">💾 Enregistrer les modifications</button>
                        <a href="profil.php" class="btn btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>