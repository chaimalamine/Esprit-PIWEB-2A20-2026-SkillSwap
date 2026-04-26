<?php
require_once __DIR__ . '/../../controller/groupeC.php';
require_once __DIR__ . '/../../controller/postC.php';

$gc = new groupeC();
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$groupe = $gc->getGroupeById($id);
$errors = [];

if (!$groupe) {
    die("Groupe non trouvé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $pc = new postC();
    $errors = $pc->validate($_POST);
    
    if (empty($errors)) {
        $post = new post($_POST['titre'], $_POST['contenu'], date('Y-m-d H:i:s'), $_POST['idgroup']);
        $pc->createPost($post);
        $_SESSION['success'] = "Post créé avec succès";
        header('Location: groupedetail.php?id=' . $_POST['idgroup']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - Créer un post</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; }
        .navbar h2 { color: #7b2ff7; }
        .navbar a { margin: 0 10px; text-decoration: none; color: #333; }
        .form-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-container h2 { margin-bottom: 20px; color: #333; }
        .form-container label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-container input, .form-container textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit; }
        .error-field { color: red; font-size: 12px; margin-bottom: 15px; margin-top: 0; }
        .btn-submit { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; }
        .btn-submit:hover { background: linear-gradient(90deg, #6a11cb, #9333ea); }
        .cancel { margin-left: 10px; color: #666; text-decoration: none; }
        .cancel:hover { color: #333; }
        footer { text-align: center; padding: 20px; background: #111; color: white; margin-top: 50px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="accueil.php">Accueil</a>
        <a href="groupes.php">Groupes</a>
        <a href="#">Explorer</a>
        <a href="#">Proposer</a>
    </div>
</div>

<div class="form-container">
    <h2>Créer un post dans "<?= htmlspecialchars($groupe['nom']) ?>"</h2>
    
    <form method="POST">
        <input type="hidden" name="idgroup" value="<?= $groupe['idgroup'] ?>">
        
        <label>Titre :</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" placeholder="Titre du post">
        <?php if(isset($errors['titre'])): ?>
            <div class="error-field"> <?= $errors['titre'] ?></div>
        <?php endif; ?>
        
        <label>Contenu :</label>
        <textarea name="contenu" rows="6" placeholder="Écrivez votre post ici..."><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
        <?php if(isset($errors['contenu'])): ?>
            <div class="error-field"> <?= $errors['contenu'] ?></div>
        <?php endif; ?>
        
        <button type="submit" class="btn-submit">Publier</button>
        <a href="groupedetail.php?id=<?= $groupe['idgroup'] ?>" class="cancel">Annuler</a>
    </form>
</div>

<footer>© 2026 SkillSwap</footer>

</body>
</html>