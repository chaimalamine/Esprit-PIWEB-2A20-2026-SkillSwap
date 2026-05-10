<?php
require_once __DIR__ . '/../../../controller/commentaireC.php';

// ========== VALIDATION DANS LA VUE ==========
function validateCommentaire($data) {
    $errors = [];
    
    if (empty(trim($data['contenu']))) {
        $errors['contenu'] = "Le commentaire est requis";
    } elseif (strlen(trim($data['contenu'])) > 500) {
        $errors['contenu'] = "Le commentaire ne peut pas dépasser 500 caractères";
    } elseif (strlen(trim($data['contenu'])) < 2) {
        $errors['contenu'] = "Le commentaire doit contenir au moins 2 caractères";
    }
    
    return $errors;
}
// =========================================

$cc = new commentaireC();
$commentaire = $cc->getCommentaireById($_GET['id']);
$errors = [];

if (!$commentaire) {
    die("Commentaire non trouvé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $errors = validateCommentaire($_POST);  // ← Appel à la fonction dans la vue
    
    if (empty($errors)) {
        $comObj = new commentaire($_POST['contenu'], $commentaire['datecom'], $commentaire['idpost']);
        $comObj->setIdcom($_GET['id']);
        $cc->updateCommentaire($_GET['id'], $comObj);
        $_SESSION['success'] = "Commentaire modifié avec succès";
        header('Location: index.php');
        exit();
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
        .form-container label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-container textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }
        .error-field { color: red; font-size: 12px; margin-bottom: 15px; margin-top: 0; }
        button { background: #6a11cb; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .cancel { margin-left: 10px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="../groupes/index.php"> Groupes</a>
    <a href="../posts/index.php"> Posts</a>
    <a href="index.php"> Commentaires</a>
</div>

<div class="main">
    <div class="form-container">
        <h2>Modifier le commentaire</h2>
        
        <form method="POST">
            <label>Contenu :</label>
            <textarea name="contenu" rows="5"><?= htmlspecialchars($_POST['contenu'] ?? $commentaire['contenu']) ?></textarea>
            <?php if(isset($errors['contenu'])): ?>
                <div class="error-field"> <?= $errors['contenu'] ?></div>
            <?php endif; ?>
            
            <button type="submit">Modifier</button>
            <a href="index.php" class="cancel">Annuler</a>
        </form>
    </div>
</div>
</body>
</html>