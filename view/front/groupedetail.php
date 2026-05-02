<?php
session_start();
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

// Filtrer et supprimer les posts éphémères expirés
$postsFiltres = [];
foreach ($posts as $post) {
    $postId = $post['idpost'];
    $expiration = isset($_SESSION['post_ephemere_' . $postId]) ? $_SESSION['post_ephemere_' . $postId] : null;
    
    if ($expiration !== null && $expiration <= time()) {
        $pc->deletePost($postId);
        unset($_SESSION['post_ephemere_' . $postId]);
    } else {
        $postsFiltres[] = $post;
    }
}
$posts = $postsFiltres;
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
        .post-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); scroll-margin-top: 80px; }
        .post-title { color: #7b2ff7; margin-bottom: 10px; display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
        .post-content { margin-bottom: 10px; line-height: 1.5; }
        .post-meta { font-size: 12px; color: #888; margin-bottom: 15px; }
        .post-actions { margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-like { background: #ff4757; color: white; border: none; padding: 5px 12px; border-radius: 20px; cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .btn-like:hover { transform: scale(1.05); }
        .btn-copy { background: #4CAF50; color: white; border: none; padding: 5px 12px; border-radius: 20px; cursor: pointer; font-size: 12px; }
        .btn-copy:hover { background: #45a049; }
        .copy-msg { color: green; font-size: 12px; margin-left: 10px; display: none; }
        .ephemere-badge {
            background: #ff4757;
            color: white;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .comment-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; }
        .comment-section h4 { margin-bottom: 10px; color: #555; }
        .comment { background: #f9f9f9; padding: 10px; border-radius: 10px; margin-bottom: 10px; }
        .comment-text { font-size: 14px; }
        .comment-meta { font-size: 11px; color: #888; margin-top: 5px; }
        .comment-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit; resize: vertical; }
        .btn-comment { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 8px 20px; border-radius: 20px; margin-top: 10px; cursor: pointer; }
        .suggestion-buttons { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
        .suggestion-btn { background: #f0f0f0; border: 1px solid #ddd; color: #333; padding: 4px 10px; border-radius: 20px; font-size: 11px; cursor: pointer; transition: all 0.2s; }
        .suggestion-btn:hover { background: #7b2ff7; color: white; border-color: #7b2ff7; }
        footer { text-align: center; padding: 20px; background: #111; color: white; margin-top: 50px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
        .post-card:target {
            background: #fff3cd;
            border: 2px solid #ffc107;
            transition: background 0.5s;
        }
        
        html {
            scroll-behavior: smooth;
        }
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
            <div class="post-card" id="post-<?= $post['idpost'] ?>">
                <h3 class="post-title">
                    <?= htmlspecialchars($post['titre']) ?>
                    <?php if (isset($_SESSION['post_ephemere_' . $post['idpost']])): 
                        $tempsRestant = $_SESSION['post_ephemere_' . $post['idpost']] - time();
                        $minutesRestantes = ceil($tempsRestant / 60);
                    ?>
                        <span class="ephemere-badge">🔥 Éphémère (<?= $minutesRestantes ?> min)</span>
                    <?php endif; ?>
                </h3>
                <div class="post-content"><?= nl2br(htmlspecialchars($post['contenu'])) ?></div>
                <div class="post-meta">Posté le <?= $post['datepost'] ?></div>
                
                <div class="post-actions">
                    <button class="btn-like" data-id="<?= $post['idpost'] ?>" onclick="toggleLike(this, <?= $post['idpost'] ?>)">
                        ❤️ J'aime (<span class="like-count-<?= $post['idpost'] ?>">0</span>)
                    </button>
                    <button class="btn-copy" onclick="copyLink(<?= $groupe['idgroup'] ?>, <?= $post['idpost'] ?>)">
                        🔗 Copier le lien
                    </button>
                    <span id="copyMsg-<?= $post['idpost'] ?>" class="copy-msg">✅ Lien copié !</span>
                </div>
                
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
                        <p>Aucun commentaire pour le moment.</p>
                    <?php endif; ?>
                    
                    <div class="suggestion-buttons">
                        <button type="button" class="suggestion-btn" onclick="addSuggestion('Merci pour ce partage !', <?= $post['idpost'] ?>)">👍 Merci</button>
                        <button type="button" class="suggestion-btn" onclick="addSuggestion('Bonne idée !', <?= $post['idpost'] ?>)">💡 Bonne idée</button>
                        <button type="button" class="suggestion-btn" onclick="addSuggestion('Je suis d\'accord', <?= $post['idpost'] ?>)">✅ D'accord</button>
                        <button type="button" class="suggestion-btn" onclick="addSuggestion('Super travail !', <?= $post['idpost'] ?>)">⭐ Super</button>
                        <button type="button" class="suggestion-btn" onclick="addSuggestion('À tester, merci !', <?= $post['idpost'] ?>)">🧪 À tester</button>
                    </div>
                    
                    <form class="comment-form" method="POST" action="createCommentaire.php">
                        <input type="hidden" name="idpost" value="<?= $post['idpost'] ?>">
                        <input type="hidden" name="idgroupe" value="<?= $groupe['idgroup'] ?>">
                        <textarea name="contenu" id="commentTextarea_<?= $post['idpost'] ?>" rows="2" placeholder="Ajouter un commentaire..."></textarea>
                        <button type="submit" class="btn-comment">Commenter</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun post pour le moment. Soyez le premier à poster !</p>
        <?php endif; ?>
    </div>
</div>

<footer>© 2026 SkillSwap</footer>

<script>
function toggleLike(btn, postId) {
    let countSpan = btn.querySelector('span');
    let currentLikes = parseInt(countSpan.innerText);
    
    let likedPosts = JSON.parse(localStorage.getItem('likedPosts') || '{}');
    
    if (likedPosts[postId]) {
        countSpan.innerText = currentLikes - 1;
        likedPosts[postId] = false;
        btn.style.opacity = '0.6';
    } else {
        countSpan.innerText = currentLikes + 1;
        likedPosts[postId] = true;
        btn.style.opacity = '1';
    }
    
    localStorage.setItem('likedPosts', JSON.stringify(likedPosts));
    
    btn.style.transform = 'scale(1.1)';
    setTimeout(() => { btn.style.transform = 'scale(1)'; }, 200);
}

function copyLink(groupeId, postId) {
    const url = window.location.origin + '/skillswap1/view/front/groupedetail.php?id=' + groupeId + '#post-' + postId;
    navigator.clipboard.writeText(url).then(function() {
        const msg = document.getElementById('copyMsg-' + postId);
        msg.style.display = 'inline';
        setTimeout(function() { msg.style.display = 'none'; }, 2000);
    });
}

function addSuggestion(texte, postId) {
    let textarea = document.getElementById('commentTextarea_' + postId);
    let currentText = textarea.value;
    if (currentText.trim() === '') {
        textarea.value = texte;
    } else {
        textarea.value = currentText + ' ' + texte;
    }
    textarea.focus();
}

document.addEventListener('DOMContentLoaded', function() {
    let likedPosts = JSON.parse(localStorage.getItem('likedPosts') || '{}');
    document.querySelectorAll('.btn-like').forEach(btn => {
        let postId = btn.getAttribute('data-id');
        if (likedPosts[postId]) {
            btn.style.opacity = '1';
        } else {
            btn.style.opacity = '0.6';
        }
    });
});
</script>

</body>
</html>