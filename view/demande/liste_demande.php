<?php
include __DIR__ . '/../../controller/demandeC.php';
$dc = new DemandeC();
$liste = $dc->listeDemandes();
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>
    <h1>Demandes d'échange</h1>
    <a href="<?= $base ?>/view/demande/ajout_demande.php" class="btn btn-purple" style="margin-bottom:24px">+ Ajouter une demande</a>
    <br><br>
    <?php foreach ($liste as $d): ?>
    <div class="card">
        <h3><?= htmlspecialchars($d['titre']) ?></h3>
        <p><?= htmlspecialchars($d['description']) ?></p>
        <div class="actions">
            <a href="<?= $base ?>/view/demande/update_demande.php?id=<?= $d['id'] ?>" class="btn btn-gray">Modifier</a>
            <a href="<?= $base ?>/view/demande/delete_demande.php?id=<?= $d['id'] ?>" class="btn btn-red" onclick="return confirm('Supprimer cette demande ?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (!$liste->rowCount()): ?>
        <p class="empty">Aucune demande pour l'instant.</p>
    <?php endif; ?>
</div></body></html>
