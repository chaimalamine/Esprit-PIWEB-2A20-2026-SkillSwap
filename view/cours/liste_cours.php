<?php
include __DIR__ . '/../../controller/coursC.php';
$cc    = new CoursC();
$liste = $cc->listeCoursApprouves();
$base  = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>

<div class="page-hero">
    <div>
        <h1>📚 Explorer les cours</h1>
        <p>Découvrez les cours proposés et approuvés par la communauté SkillSwap.</p>
    </div>
    <a href="<?= $base ?>/view/cours/ajout_cours.php" class="btn btn-white">+ Proposer un cours</a>
</div>

<?php
$rows = $liste->fetchAll();
if (empty($rows)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucun cours disponible pour l'instant</h3>
        <p>Soyez le premier à proposer un cours — il sera visible après validation.</p>
    </div>
<?php else: foreach ($rows as $c):
    $niveauLabel = ['debutant'=>'Débutant','intermediaire'=>'Intermédiaire','avance'=>'Avancé'][$c['niveau'] ?? 'debutant'];
?>
<div class="card">
    <div class="card-top">
        <h3><?= htmlspecialchars($c['titre']) ?></h3>
    </div>
    <?php if (!empty($c['categorie']) || !empty($c['niveau'])): ?>
    <div class="card-meta">
        <?php if (!empty($c['categorie'])): ?>
        <span class="tag">🏷️ <?= htmlspecialchars($c['categorie']) ?></span>
        <?php endif; ?>
        <span class="tag">📊 <?= $niveauLabel ?></span>
    </div>
    <?php endif; ?>
    <p><?= htmlspecialchars($c['description']) ?></p>
    <div class="actions">
        <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $c['id'] ?>" class="btn btn-purple btn-sm">Voir le cours</a>
        <a href="<?= $base ?>/view/cours/update_cours.php?id=<?= $c['id'] ?>" class="btn btn-gray btn-sm">Modifier</a>
        <a href="<?= $base ?>/view/cours/delete_cours.php?id=<?= $c['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
    </div>
</div>
<?php endforeach; endif; ?>
</div></body></html>
