<?php
include __DIR__ . '/../../controller/demandeC.php';
$dc    = new DemandeC();
$liste = $dc->listeDemandesApprouvees();
$base  = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>

<div class="page-hero">
    <div>
        <h1>📋 Demandes d'échange</h1>
        <p>Consultez les demandes validées et trouvez un échange qui vous correspond.</p>
    </div>
    <a href="<?= $base ?>/view/demande/ajout_demande.php" class="btn btn-white">+ Soumettre une demande</a>
</div>

<?php
$rows = $liste->fetchAll();
if (empty($rows)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucune demande disponible pour l'instant</h3>
        <p>Soumettez une demande — elle sera visible après validation par l'administrateur.</p>
    </div>
<?php else: foreach ($rows as $d):
    $urgenceLabel = ['normale'=>'Normale','urgent'=>'🔥 Urgent','flexible'=>'Flexible'][$d['urgence'] ?? 'normale'];
?>
<div class="card">
    <div class="card-top">
        <h3><?= htmlspecialchars($d['titre']) ?></h3>
    </div>
    <?php if (!empty($d['competence_souhaitee']) || !empty($d['urgence'])): ?>
    <div class="card-meta">
        <?php if (!empty($d['competence_souhaitee'])): ?>
        <span class="tag">🎯 <?= htmlspecialchars($d['competence_souhaitee']) ?></span>
        <?php endif; ?>
        <span class="tag">⚡ <?= $urgenceLabel ?></span>
    </div>
    <?php endif; ?>
    <p><?= htmlspecialchars($d['description']) ?></p>
    <div class="actions">
        <a href="<?= $base ?>/view/demande/update_demande.php?id=<?= $d['id'] ?>" class="btn btn-gray btn-sm">Modifier</a>
        <a href="<?= $base ?>/view/demande/delete_demande.php?id=<?= $d['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Supprimer cette demande ?')">Supprimer</a>
    </div>
</div>
<?php endforeach; endif; ?>
</div></body></html>
