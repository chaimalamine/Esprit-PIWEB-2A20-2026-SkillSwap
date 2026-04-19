<?php
include_once "../../controller/groupeC.php";
include_once "../../controller/postC.php";
include_once "../../controller/commentaireC.php";

$gc = new groupeC();
$pc = new postC();
$cc = new commentaireC();

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$groupe = $gc->getGroupeById($id);

if (!$groupe) {
    die("Groupe non trouvé");
}

$posts = $pc->getPostsByGroup($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - <?= htmlspecialchars($groupe['nom']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; }
        .navbar h2 { color: #7b2ff7; }
        .navbar a { margin: 0 10px; text-decoration: none; color: #333; }
        .container { max-width: 800px; margin: 40px auto; padding: 20px; }
        .group-header { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .group-header h1 { color: #7b2ff7; }
        .btn-back { background: #6a11cb; color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; margin-top: 20px; }
        .btn-create { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; margin-top: 20px; margin-left: 10px; }
        .posts-section { margin-top: 40px; }
        .post-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .post-title { color: #7b2ff7; margin-bottom: 10px; }
        .post-content { margin-bottom: 10px; line-height: 1.5; }
        .post-meta { font-size: 12px; color: #888; margin-bottom: 15px; }
        .comment-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; }
        .comment-section h4 { margin-bottom: 10px; color: #555; }
        .comment { background: #f9f9f9; padding: 10px; border-radius: 10px; margin-bottom: 10px; }
        .comment-text { font-size: 14px; }
        .comment-meta { font-size: 11px; color: #888; margin-top: 5px; }
        .comment-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit; resize: vertical; }
        .btn-comment { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 8px 20px; border-radius: 20px; margin-top: 10px; cursor: pointer; }
        footer { text-align: center; padding: 20px; background: #111; color: white; margin-top: 50px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
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

<div class="container">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div class="error"><?= implode('<br>', $_SESSION['errors']) ?></div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="group-header">
        <h1><?= htmlspecialchars($groupe['nom']) ?></h1>
        <p><?= nl2br(htmlspecialchars($groupe['description'])) ?></p>
        <p style="margin-top: 15px; color: #888;">Créé le <?= $groupe['datecreation'] ?></p>
        
        <button class="btn-back" onclick="window.location.href='groupes.php'">← Retour aux groupes</button>
        <button class="btn-create" onclick="window.location.href='postcreate.php?id=<?= $groupe['idgroup'] ?>'">+ Créer un post</button>
    </div>

    <div class="posts-section">
        <h2>Posts du groupe</h2>
        
        <?php if (!empty($posts)): ?>
            <?php foreach($posts as $post): ?>
            <div class="post-card">
                <h3 class="post-title"><?= htmlspecialchars($post['titre']) ?></h3>
                <div class="post-content"><?= nl2br(htmlspecialchars($post['contenu'])) ?></div>
                <div class="post-meta">Posté le <?= $post['datepost'] ?></div>
                
                <!-- SECTION COMMENTAIRES -->
                <div class="comment-section">
                    <h4>Commentaires</h4>
                    
                    <?php
                    $commentaires = $cc->getCommentairesByPost($post['idpost']);
                    ?>
                    
                    <?php if (!empty($commentaires)): ?>
                        <?php foreach($commentaires as $commentaire): ?>
                        <div class="comment">
                            <div class="comment-text"><?= nl2br(htmlspecialchars($commentaire['contenu'])) ?></div>
                            <div class="comment-meta">Posté le <?= $commentaire['datecom'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 12px; color: #888;">Aucun commentaire pour le moment.</p>
                    <?php endif; ?>
                    
                    <!-- Formulaire d'ajout de commentaire -->
                    <form class="comment-form" method="POST" action="createCommentaire.php">
                        <input type="hidden" name="idpost" value="<?= $post['idpost'] ?>">
                        <input type="hidden" name="idgroupe" value="<?= $groupe['idgroup'] ?>">
                        <textarea name="contenu" rows="2" placeholder="Ajouter un commentaire..."></textarea>
                        <button type="submit" class="btn-comment">Commenter</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888;">Aucun post pour le moment. Soyez le premier à poster !</p>
        <?php endif; ?>
    </div>
</div>

<footer>© 2026 SkillSwap</footer>

</body>
</html>