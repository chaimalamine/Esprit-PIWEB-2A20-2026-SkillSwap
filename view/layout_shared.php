<?php
// Ce fichier contient le CSS et la navbar commune
$base = '/SkillSwap';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; min-height: 100vh; }

        /* Navbar */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 40px; background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .navbar h2 { color: #7b2ff7; font-size: 20px; }
        .navbar a { margin: 0 10px; text-decoration: none; color: #333; font-size: 15px; }
        .navbar a:hover { color: #7b2ff7; }

        /* Content */
        .content { padding: 50px 60px; max-width: 1100px; margin: 0 auto; }
        .content h1 { color: #7b2ff7; font-size: 28px; margin-bottom: 20px; }

        /* Buttons */
        .btn {
            display: inline-block; padding: 9px 22px; border-radius: 20px;
            text-decoration: none; font-size: 14px; font-weight: 500;
            cursor: pointer; border: none; transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-purple { background: #7b2ff7; color: white; }
        .btn-red    { background: #ef4444; color: white; }
        .btn-gray   { background: transparent; color: #7b2ff7; }

        /* Cards */
        .card {
            background: white; border-radius: 16px;
            padding: 24px 28px; margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .card h3 { color: #7b2ff7; font-size: 18px; margin-bottom: 6px; }
        .card p  { color: #555; font-size: 14px; margin-bottom: 16px; }
        .card .actions { display: flex; align-items: center; gap: 10px; }

        /* Form */
        .form-card {
            background: white; border-radius: 16px;
            padding: 36px; max-width: 520px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .form-card input, .form-card textarea {
            width: 100%; padding: 12px 16px; margin-bottom: 16px;
            border: 1.5px solid #e0d9f7; border-radius: 10px;
            font-size: 14px; font-family: inherit; outline: none;
            transition: border-color 0.2s;
        }
        .form-card input:focus, .form-card textarea:focus { border-color: #7b2ff7; }
        .form-card textarea { height: 120px; resize: vertical; }
        .form-card .btn { margin-right: 10px; }

        /* Empty state */
        .empty { color: #999; font-size: 15px; margin-top: 10px; }
    </style>
    <script src="<?= $base ?>/js/skillswap.js" defer></script>
</head>
<body>
<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="<?= $base ?>/index.php">Accueil</a>
        <a href="<?= $base ?>/view/cours/liste_cours.php">Cours</a>
        <a href="<?= $base ?>/view/demande/liste_demande.php">Demandes</a>
        <a href="<?= $base ?>/view/cours/ajout_cours.php">Proposer</a>
    </div>
</div>
<div class="content">
