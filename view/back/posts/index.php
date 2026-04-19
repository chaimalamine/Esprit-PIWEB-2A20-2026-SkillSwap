<?php
require_once __DIR__ . '/../../../controller/postC.php';

$pc = new postC();
$posts = $pc->listPosts();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Posts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; margin-left: 220px; padding: 20px; }
        .topbar { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .admin-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .admin-table th { background: #6a11cb; color: white; }
        .btn-edit { background: #2575fc; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .btn-delete { background: #ef4444; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-left: 5px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="../groupes/index.php">📁 Groupes</a>
    <a href="index.php">📝 Posts</a>
    <a href="../commentaires/index.php">💬 Commentaires</a>
</div>

<div class="main">
    <div class="topbar">
        <h3>Gestion des Posts</h3>
    </div>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <table class="admin-table">
        <thead>
            <tr><th>ID</th><th>Titre</th><th>Contenu</th><th>Groupe</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php if(!empty($posts)): ?>
                <?php foreach($posts as $post): ?>
                <tr>
                    <td><?= $post['idpost'] ?></td>
                    <td><?= htmlspecialchars($post['titre']) ?></td>
                    <td><?= htmlspecialchars(substr($post['contenu'], 0, 50)) ?>...</td>
                    <td><?= $post['idgroup'] ?></td>
                    <td><?= $post['datepost'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $post['idpost'] ?>" class="btn-edit">Modifier</a>
                        <a href="delete.php?id=<?= $post['idpost'] ?>" class="btn-delete" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </a>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <td><td colspan="6">Aucun post trouvé</a></td>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>