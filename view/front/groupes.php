<?php
require_once __DIR__ . '/../../controller/groupeC.php';

$gc = new groupeC();

// Vérifier si on doit exporter
if (isset($_GET['export']) && $_GET['export'] == 1) {
    $groupes = $gc->listGroupes();
    
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="groupes_' . date('Y-m-d') . '.html"');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Groupes</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            h1 { color: #7b2ff7; text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background: #7b2ff7; color: white; }
            .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; }
        </style>
    </head>
    <body>
        <h1> Liste des Groupes - SkillSwap</h1>
        <p>Date : <?= date('d/m/Y H:i') ?></p>
        <table>
            <thead>
                <tr><th>ID</th><th>Nom</th><th>Description</th><th>Date création</th></tr>
            </thead>
            <tbody>
                <?php foreach($groupes as $g): ?>
                <tr>
                    <td><?= $g['idgroup'] ?></td>
                    <td><?= htmlspecialchars($g['nom']) ?></td>
                    <td><?= htmlspecialchars(substr($g['description'], 0, 100)) ?>...</td>
                    <td><?= $g['datecreation'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="footer">Généré par SkillSwap</div>
    </body>
    </html>
    <?php
    exit();
}

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $groupes = $gc->searchGroupes($search);
} else {
    $groupes = $gc->listGroupes();
}
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
        .search-container { text-align: center; margin-bottom: 30px; }
        .search-container input { padding: 10px 20px; width: 300px; border-radius: 25px; border: 1px solid #ddd; font-size: 16px; }
        .search-container button { padding: 10px 20px; background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; border-radius: 25px; cursor: pointer; }
        .search-container a { padding: 10px 20px; background: #ccc; color: #333; border-radius: 25px; text-decoration: none; }
        .export-btn { background: #dc3545; color: white; padding: 8px 15px; border-radius: 20px; text-decoration: none; display: inline-block; }
        .export-btn:hover { background: #c82333; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
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

        <!-- Barre de recherche -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un groupe...">
                <button type="submit"> Rechercher</button>
                <?php if(!empty($search)): ?>
                    <a href="groupes.php">✖ Effacer</a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if(empty($groupes)): ?>
            <p style="text-align: center; color: #888;">Aucun groupe trouvé pour "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>
        
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