<?php
require_once __DIR__ . '/../../../controller/commentaireC.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du commentaire manquant");
}

$cc = new commentaireC();
$commentaire = $cc->getCommentaireById($_GET['id']);

if (!$commentaire) {
    die("Commentaire non trouvé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $errors = $cc->validate($_POST);
    
    if (empty($errors)) {
        $comObj = new commentaire($_POST['contenu'], $commentaire['datecom'], $commentaire['idpost']);
        $comObj->setIdcom($_GET['id']);
        
        $result = $cc->updateCommentaire($_GET['id'], $comObj);
        
        if ($result) {
            $_SESSION['success'] = "Commentaire modifié avec succès";
            header('Location: index.php');
            exit();
        } else {
            $error = "Erreur lors de la modification";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Modifier un commentaire</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; margin-left: 220px; padding: 20px; }
        .form-container { max-width: 600px; background: white; padding: 30px; border-radius: 15px; margin-top: 20px; }
        .form-container textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        .error { color: red; font-size: 12px; margin-bottom: 10px; }
        button { background: #6a11cb; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .cancel { margin-left: 10px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="../groupes/index.php">📁 Groupes</a>
    <a href="../posts/index.php">📝 Posts</a>
    <a href="index.php">💬 Commentaires</a>
</div>

<div class="main">
    <div class="form-container">
        <h2>Modifier le commentaire</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if(isset($errors) && !empty($errors)): ?>
            <?php foreach($errors as $error): ?>
                <div class="error"><?= $error ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form method="POST">
            <label>Contenu :</label>
            <textarea name="contenu" rows="5"><?= htmlspecialchars($commentaire['contenu']) ?></textarea>
            
            <button type="submit">Modifier</button>
            <a href="index.php" class="cancel">Annuler</a>
        </form>
    </div>
</div>
</body>
</html>