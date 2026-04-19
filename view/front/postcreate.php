<?php
include_once "../../controller/groupeC.php";

$gc = new groupeC();
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$groupe = $gc->getGroupeById($id);

if (!$groupe) {
    die("Groupe non trouvé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    include_once "../../controller/postC.php";
    
    $pc = new postC();
    $errors = $pc->validate($_POST);
    
    if (empty($errors)) {
        $post = new post($_POST['titre'], $_POST['contenu'], date('Y-m-d H:i:s'), $_POST['idgroup']);
        $pc->createPost($post);
        $_SESSION['success'] = "Post créé avec succès";
        header('Location: groupedetail.php?id=' . $_POST['idgroup']);
        exit();
    } else {
        $_SESSION['errors'] = $errors;
        header('Location: postcreate.php?id=' . $_POST['idgroup']);
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
        .form-container input, .form-container textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit; }
        .error { color: red; font-size: 12px; margin-bottom: 10px; }
        .btn-submit { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; }
        footer { text-align: center; padding: 20px; background: #111; color: white; margin-top: 50px; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="accueil.php">Accueil</a>
        <a href="groupes.php">Groupes</a>
    </div>
</div>

<div class="form-container">
    <h2>Créer un post dans "<?= htmlspecialchars($groupe['nom']) ?>"</h2>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <?php foreach($_SESSION['errors'] as $error): ?>
            <div class="error"><?= $error ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="idgroup" value="<?= $groupe['idgroup'] ?>">
        <input type="text" name="titre" placeholder="Titre du post">
        <textarea name="contenu" rows="6" placeholder="Écrivez votre post ici..."></textarea>
        <button type="submit" class="btn-submit">Publier</button>
        <a href="groupedetail.php?id=<?= $groupe['idgroup'] ?>">Annuler</a>
    </form>
</div>

<footer>© 2026 SkillSwap</footer>

<script>
    document.querySelector('form')?.addEventListener('submit', function(e) {
        var titre = document.querySelector('input[name="titre"]').value.trim();
        var contenu = document.querySelector('textarea[name="contenu"]').value.trim();
        
        if (titre.length < 3) {
            alert("Le titre doit contenir au moins 3 caractères");
            e.preventDefault();
            return false;
        }
        
        if (contenu.length < 10) {
            alert("Le contenu doit contenir au moins 10 caractères");
            e.preventDefault();
            return false;
        }
        
        return true;
    });
</script>

</body>
</html>