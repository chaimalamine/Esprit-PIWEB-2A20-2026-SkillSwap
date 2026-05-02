<?php
require_once __DIR__ . '/../../controller/groupeC.php';
require_once __DIR__ . '/../../controller/postC.php';
require_once __DIR__ . '/../../model/post.php';

$gc = new groupeC();
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$groupe = $gc->getGroupeById($id);

if (!$groupe) {
    die("Groupe non trouvé");
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    // Validation
    if (empty(trim($_POST['titre']))) {
        $errors['titre'] = "Le titre est requis";
    } elseif (strlen(trim($_POST['titre'])) < 3) {
        $errors['titre'] = "Le titre doit contenir au moins 3 caractères";
    }
    
    if (empty(trim($_POST['contenu']))) {
        $errors['contenu'] = "Le contenu est requis";
    } elseif (strlen(trim($_POST['contenu'])) < 10) {
        $errors['contenu'] = "Le contenu doit contenir au moins 10 caractères";
    }
    
    if (empty($errors)) {
        $pc = new postC();
        $post = new post($_POST['titre'], $_POST['contenu'], date('Y-m-d H:i:s'), $_POST['idgroup']);
        $result = $pc->createPost($post);
        
        if ($result) {
            $postId = $pc->getLastInsertId();
            
            if (isset($_POST['est_ephemere']) && $_POST['est_ephemere'] == 1) {
                $dureeMinutes = intval($_POST['duree_ephemere']);
                $expiration = time() + ($dureeMinutes * 60);
                $_SESSION['post_ephemere_' . $postId] = $expiration;
                $_SESSION['success'] = " Post éphémère créé (disparaît dans $dureeMinutes min)";
            } else {
                $_SESSION['success'] = " Post créé avec succès";
            }
            
            header('Location: groupedetail.php?id=' . $_POST['idgroup']);
            exit();
        }
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
        .form-container { max-width: 700px; margin: 50px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-container h2 { color: #7b2ff7; margin-bottom: 20px; border-left: 4px solid #7b2ff7; padding-left: 15px; }
        .form-container label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-container input, .form-container textarea, .form-container select { width: 100%; padding: 12px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit; }
        .error-field { color: red; font-size: 12px; margin-bottom: 15px; }
        .btn-submit { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; }
        .cancel { margin-left: 15px; color: #666; text-decoration: none; }
        .ephemere-options { display: none; margin-top: 10px; padding: 10px; background: #f9f9f9; border-radius: 10px; }
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
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?php foreach($_SESSION['errors'] as $error): ?>
                 <?= $error ?><br>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" id="postForm">
        <input type="hidden" name="idgroup" value="<?= $groupe['idgroup'] ?>">
        
        <label>Titre :</label>
        <input type="text" name="titre" id="titre" placeholder="Titre du post">
        <div class="error-field" id="titreError"></div>
        
        <label>Contenu :</label>
        <textarea name="contenu" id="contenu" rows="6" placeholder="Écrivez votre post ici..."></textarea>
        <div class="error-field" id="contenuError"></div>
        
        <label style="margin-top: 10px;">
            <input type="checkbox" name="est_ephemere" id="estEphemere" value="1">
             Post éphémère
        </label>
        
        <div id="dureeOptions" class="ephemere-options">
            <label>Durée :</label>
            <select name="duree_ephemere" id="duree">
                <option value="1">1 minute</option>
                <option value="2">2 minutes</option>
                <option value="5">5 minutes</option>
                <option value="10">10 minutes</option>
                <option value="30">30 minutes</option>
            </select>
        </div>
        
        <button type="submit" class="btn-submit"> Publier</button>
        <a href="groupedetail.php?id=<?= $groupe['idgroup'] ?>" class="cancel"> Annuler</a>
    </form>
</div>

<footer>© 2026 SkillSwap</footer>

<script>
document.getElementById('estEphemere').addEventListener('change', function() {
    document.getElementById('dureeOptions').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('postForm').addEventListener('submit', function(e) {
    let titre = document.getElementById('titre').value.trim();
    let contenu = document.getElementById('contenu').value.trim();
    let hasError = false;
    
    document.getElementById('titreError').innerHTML = '';
    document.getElementById('contenuError').innerHTML = '';
    
    if (titre === '') {
        document.getElementById('titreError').innerHTML = ' Le titre est requis';
        hasError = true;
    } else if (titre.length < 3) {
        document.getElementById('titreError').innerHTML = ' Le titre doit contenir au moins 3 caractères';
        hasError = true;
    }
    
    if (contenu === '') {
        document.getElementById('contenuError').innerHTML = ' Le contenu est requis';
        hasError = true;
    } else if (contenu.length < 10) {
        document.getElementById('contenuError').innerHTML = ' Le contenu doit contenir au moins 10 caractères';
        hasError = true;
    }
    
    if (hasError) {
        e.preventDefault();
        return false;
    }
});
</script>

</body>
</html>