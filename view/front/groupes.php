<?php
require_once __DIR__ . '/../../controller/groupeC.php';
require_once __DIR__ . '/../../controller/postC.php';

$gc = new groupeC();
$pc = new postC();

// Récupérer tous les groupes
$groupes = $gc->listGroupes();

// Supprimer les doublons par ID (plus sûr)
$groupesUniques = [];
foreach ($groupes as $groupe) {
    $groupesUniques[$groupe['idgroup']] = $groupe;
}
$groupes = array_values($groupesUniques);

// Compter les posts et trouver le groupe recommandé
$maxPosts = -1;
$groupeRecommandé = null;

foreach ($groupes as $key => $groupe) {
    $posts = $pc->getPostsByGroup($groupe['idgroup']);
    $groupes[$key]['nb_posts'] = count($posts);
    
    if ($groupes[$key]['nb_posts'] > $maxPosts) {
        $maxPosts = $groupes[$key]['nb_posts'];
        $groupeRecommandé = $groupes[$key];
    }
}

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $groupes = $gc->searchGroupes($search);
    $groupesUniques = [];
    foreach ($groupes as $g) {
        $groupesUniques[$g['idgroup']] = $g;
    }
    $groupes = array_values($groupesUniques);
    foreach ($groupes as $key => $g) {
        $posts = $pc->getPostsByGroup($g['idgroup']);
        $groupes[$key]['nb_posts'] = count($posts);
    }
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
        .group-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .group-card:hover { transform: translateY(-5px); }
        .group-card h3 { color: #7b2ff7; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .group-card p { color: #666; margin-bottom: 15px; }
        .group-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #888; }
        .badge { background: #e9d5ff; color: #7b2ff7; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        .recommended-badge {
            background: linear-gradient(135deg, #ffd700, #ff8c00);
            color: white;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .new-badge {
            background: linear-gradient(135deg, #ff4757, #ff6b81);
            color: white;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .group-btn-outline { background: transparent; border: 1px solid #7b2ff7; color: #7b2ff7; padding: 8px 15px; border-radius: 20px; cursor: pointer; width: 48%; transition: all 0.3s; }
        .group-btn-outline:hover { background: #7b2ff7; color: white; }
        footer { text-align: center; padding: 20px; background: #111; color: white; }
        .section { text-align: center; }
        .search-container { text-align: center; margin-bottom: 30px; }
        .search-container input { padding: 10px 20px; width: 300px; border-radius: 25px; border: 1px solid #ddd; font-size: 16px; }
        .search-container button { padding: 10px 20px; background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; border-radius: 25px; cursor: pointer; }
        .search-container a { padding: 10px 20px; background: #ccc; color: #333; border-radius: 25px; text-decoration: none; }
        .recommended-header {
            background: linear-gradient(135deg, #ffd700, #ff8c00);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
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

<div class="groups-container">
    <div class="section">
        <h2>Tous les groupes</h2>

        <!-- Groupe recommandé (affiché UNE SEULE FOIS en haut) -->
        <?php if ($groupeRecommandé): ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <div class="recommended-header">
                 Groupe recommandé : "<?= htmlspecialchars($groupeRecommandé['nom']) ?>" (<?= $groupeRecommandé['nb_posts'] ?> posts)
            </div>
        </div>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un groupe...">
                <button type="submit"> Rechercher</button>
                <?php if(!empty($search)): ?>
                    <a href="groupes.php"> Effacer</a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if(empty($groupes)): ?>
            <p>Aucun groupe trouvé</p>
        <?php endif; ?>
        
        <div class="groups-grid">
            <?php foreach($groupes as $groupe): ?>
            <?php
            $dateCreation = date('Y-m-d', strtotime($groupe['datecreation']));
            $aujourdhui = date('Y-m-d');
            $estNouveau = ($dateCreation == $aujourdhui);
            ?>
            <div class="group-card">
                <h3>
                    <?= htmlspecialchars($groupe['nom']) ?>
                    <?php if ($estNouveau): ?>
                        <span class="new-badge"> Nouveau</span>
                    <?php endif; ?>
                </h3>
                <p><?= htmlspecialchars(substr($groupe['description'], 0, 100)) ?>...</p>
                <div class="group-meta">
                    <span> <?= $groupe['nb_posts'] ?> posts</span>
                    <span class="badge"> <?= $groupe['datecreation'] ?></span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="group-btn-outline" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Rejoindre</button>
                    <button class="group-btn-outline" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Voir</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<footer>© 2026 SkillSwap</footer>

<script>
let currentRecommendedId = <?= $groupeRecommandé ? $groupeRecommandé['idgroup'] : 0 ?>;
let oldRecommendedId = localStorage.getItem('recommendedGroupId');

if (oldRecommendedId && parseInt(oldRecommendedId) !== currentRecommendedId && currentRecommendedId !== 0) {
    setTimeout(() => {
        alert(' Le groupe recommandé a changé !');
    }, 500);
}

localStorage.setItem('recommendedGroupId', currentRecommendedId);
</script>

</body>
</html>