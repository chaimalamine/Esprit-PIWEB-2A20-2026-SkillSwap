<?php
require_once __DIR__ . '/../../controller/groupeC.php';

$gc = new groupeC();
$groupes = $gc->listGroupes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - Groupes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; }
        .navbar h2 { color: #7b2ff7; }
        .navbar a { margin: 0 10px; text-decoration: none; color: #333; }
        .groups-container { background: #f9fafb; padding: 50px 20px; }
        .groups-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto; }
        .group-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .group-card h3 { color: #7b2ff7; margin-bottom: 10px; }
        .group-card p { color: #666; margin-bottom: 15px; }
        .group-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #888; }
        .badge { background: #e9d5ff; color: #7b2ff7; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        .group-btn { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; width: 100%; margin-top: 10px; }
        .group-btn-outline { background: transparent; border: 1px solid #7b2ff7; color: #7b2ff7; padding: 8px 15px; border-radius: 20px; cursor: pointer; width: 48%; }
        footer { text-align: center; padding: 20px; background: #111; color: white; }
        .section { text-align: center; }
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

<div class="groups-container">
    <div class="section">
        <h2>Tous les groupes</h2>
        <div class="groups-grid">
            <?php foreach($groupes as $groupe): ?>
            <div class="group-card">
                <h3><?= htmlspecialchars($groupe['nom']) ?></h3>
                <p><?= htmlspecialchars(substr($groupe['description'], 0, 100)) ?>...</p>
                <div class="group-meta">
                    <span>0 membres</span>
                    <span class="badge"><?= $groupe['datecreation'] ?></span>
                </div>
                <button class="group-btn-outline" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Rejoindre</button>
                <button class="group-btn-outline" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Voir</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<footer>© 2026 SkillSwap</footer>

</body>
</html>