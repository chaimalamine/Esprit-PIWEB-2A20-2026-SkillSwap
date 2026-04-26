<?php
require_once __DIR__ . '/../../../controller/groupeC.php';

$gc = new groupeC();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $errors = $gc->validate($_POST);
    
    if (empty($errors)) {
        $groupe = new groupe($_POST['nom'], $_POST['description'], date('Y-m-d'));
        $gc->createGroupe($groupe);
        $_SESSION['success'] = "Groupe créé avec succès";
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Créer un groupe</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; margin-left: 220px; padding: 20px; }
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-top: 20px; }
        .form-container h2 { margin-bottom: 20px; color: #333; }
        .form-container label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-container input, .form-container textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }
        .error-field { color: red; font-size: 12px; margin-bottom: 15px; margin-top: 0; }
        button { background: #6a11cb; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        button:hover { background: #2575fc; }
        .cancel { margin-left: 10px; color: #666; text-decoration: none; }
        .cancel:hover { color: #333; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="index.php"> Groupes</a>
    <a href="../posts/index.php"> Posts</a>
    <a href="../commentaire/index.php"> Commentaires</a>
</div>

<div class="main">
    <div class="form-container">
        <h2>Créer un nouveau groupe</h2>
        
        <form method="POST">
            <label>Nom :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Nom du groupe">
            <?php if(isset($errors['nom'])): ?>
                <div class="error-field"> <?= $errors['nom'] ?></div>
            <?php endif; ?>
            
            <label>Description :</label>
            <textarea name="description" rows="5" placeholder="Description du groupe"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <?php if(isset($errors['description'])): ?>
                <div class="error-field"> <?= $errors['description'] ?></div>
            <?php endif; ?>
            
            <button type="submit">Créer</button>
            <a href="index.php" class="cancel">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>