<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../controller/groupeC.php';
require_once __DIR__ . '/../../controller/postC.php';
require_once __DIR__ . '/../../controller/commentaireC.php';

$gc = new groupeC();
$pc = new postC();
$cc = new commentaireC();

$groupes = $gc->listGroupes();
$posts = $pc->listPosts();
$commentaires = $cc->listCommentaires();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap Admin - Groupes et Communauté</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; }
        
        /* Sidebar */
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        
        /* Main */
        .main { flex: 1; margin-left: 220px; padding: 20px; }
        .topbar { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .topbar h3 { color: #333; }
        
        /* 3 Boxes */
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: center;
            margin-top: 40px;
        }
        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 250px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-5px);
        }
        .stat-box .number {
            font-size: 42px;
            font-weight: bold;
            color: #6a11cb;
        }
        .stat-box .label {
            color: #666;
            margin-top: 10px;
            font-size: 16px;
        }
        .stat-box .btn-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 20px;
            background: #6a11cb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }
        .stat-box .btn-link:hover {
            background: #2575fc;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="dashboard.php"> Groupes & Communauté</a>
    <a href="groupes/index.php"> Groupes</a>
    <a href="posts/index.php"> Posts</a>
    <a href="commentaires/index.php"> Commentaires</a>
</div>

<div class="main">
    <div class="topbar">
        <h3>Groupes et Communauté</h3>
    </div>

    <!-- 3 BOXES STATISTIQUES -->
    <div class="stats-container">
        <div class="stat-box">
            <div class="number"><?= count($groupes) ?></div>
            <div class="label"> Groupes</div>
            <a href="groupes/index.php" class="btn-link">Gérer les groupes →</a>
        </div>
        <div class="stat-box">
            <div class="number"><?= count($posts) ?></div>
            <div class="label"> Posts</div>
            <a href="posts/index.php" class="btn-link">Gérer les posts →</a>
        </div>
        <div class="stat-box">
            <div class="number"><?= count($commentaires) ?></div>
            <div class="label"> Commentaires</div>
            <a href="commentaires/index.php" class="btn-link">Gérer les commentaires →</a>
        </div>
    </div>

    <footer>
        <p>© 2026 SkillSwap - Plateforme d'échange de compétences</p>
    </footer>
</div>

</body>
</html>