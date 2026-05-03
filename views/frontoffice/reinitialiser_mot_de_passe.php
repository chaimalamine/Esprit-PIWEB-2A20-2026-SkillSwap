<?php session_start(); 

// Vérifier qu'un token est stocké en session
if (!isset($_SESSION['reset_token'])) {
    header('Location: mot_de_passe_oublie.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser mon mot de passe - SkillSwap</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar h2 { color: #7b2ff7; text-decoration: none; }
        .navbar a { margin: 0 15px; text-decoration: none; color: #333; font-weight: 500; }
        .navbar a:hover { color: #7b2ff7; }
        .navbar button { background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; }
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 70px); padding: 20px; }
        .auth-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); width: 100%; max-width: 450px; }
        .auth-card h2 { text-align: center; color: #7b2ff7; margin-bottom: 30px; font-size: 26px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #444; }
        .form-group input { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 15px; background: #fafafa; }
        .form-group input:focus { outline: none; border-color: #7b2ff7; background: white; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; font-size: 16px; font-weight: bold; border-radius: 20px; cursor: pointer; margin-top: 10px; transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123, 47, 247, 0.4); }
        .auth-footer { text-align: center; margin-top: 25px; font-size: 14px; color: #666; }
        .auth-footer a { color: #7b2ff7; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="frontdesign.php"><h2>SkillSwap</h2></a>
        <div>
            <a href="frontdesign.php">Accueil</a>
            <a href="#">Explorer</a>
            <a href="#">Proposer</a>
            <button onclick="window.location.href='connexion.php'">Commencer</button>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Nouveau mot de passe</h2>
            
            <?php if(isset($_SESSION['erreur'])): ?>
                <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
            <?php endif; ?>
            
            <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=updatePasswordWithToken" method="POST">
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirme_mot_de_passe" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-submit">Réinitialiser</button>
            </form>

            <div class="auth-footer">
                <a href="connexion.php">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>
</html>