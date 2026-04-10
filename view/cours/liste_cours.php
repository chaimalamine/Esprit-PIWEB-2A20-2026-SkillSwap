<?php
include __DIR__ . '/../../controller/coursC.php';
$cc = new CoursC();
$liste = $cc->listeCours();
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>
    <h1>Explorer les cours</h1>
    <a href="<?= $base ?>/view/cours/ajout_cours.php" class="btn btn-purple" style="margin-bottom:24px">+ Proposer un cours</a>
    <br><br>
    <?php foreach ($liste as $c): ?>
    <div class="card">
        <h3><?= htmlspecialchars($c['titre']) ?></h3>
        <p><?= htmlspecialchars($c['description']) ?></p>
        <div class="actions">
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $c['id'] ?>" class="btn btn-purple">Voir</a>
            <a href="<?= $base ?>/view/cours/update_cours.php?id=<?= $c['id'] ?>" class="btn btn-gray">Modifier</a>
            <a href="<?= $base ?>/view/cours/delete_cours.php?id=<?= $c['id'] ?>" class="btn btn-red" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$liste->rowCount()): ?>
        <p class="empty">Aucun cours disponible pour l'instant.</p>
    <?php endif; ?>
</div></body></html>
