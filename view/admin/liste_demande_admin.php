<?php
include __DIR__ . '/../../controller/demandeC.php';
$dc   = new DemandeC();
$base = '/SkillSwap';

if (isset($_GET['approuver']))  { $dc->updateStatutDemande((int)$_GET['approuver'], 'approuve'); header('Location: '.$base.'/view/admin/liste_demande_admin.php'); exit; }
if (isset($_GET['rejeter']))    { $dc->updateStatutDemande((int)$_GET['rejeter'],   'rejete');   header('Location: '.$base.'/view/admin/liste_demande_admin.php'); exit; }
if (isset($_GET['supprimer']))  { $dc->deleteDemande((int)$_GET['supprimer']);                   header('Location: '.$base.'/view/admin/liste_demande_admin.php'); exit; }

$liste = $dc->listeDemandes();
include __DIR__ . '/layout_admin.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="color:#1a0533;font-size:22px;font-weight:700;margin-bottom:3px;">Demandes</h1>
        <p style="color:#9ca3af;font-size:13px;">Modérez et gérez toutes les demandes soumises</p>
    </div>
    <a href="<?= $base ?>/view/admin/ajout_demande_admin.php" class="btn btn-primary">Ajouter une demande</a>
</div>

<?php $rows = $liste->fetchAll();
if (empty($rows)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucune demande</h3>
        <p>Les demandes soumises par les utilisateurs apparaîtront ici.</p>
    </div>
<?php else: foreach ($rows as $d):
    $statut     = $d['statut'] ?? 'en_attente';
    $badgeClass = match($statut) { 'approuve' => 'badge-approved', 'rejete' => 'badge-rejected', default => 'badge-pending' };
    $badgeLabel = match($statut) { 'approuve' => 'Approuvée', 'rejete' => 'Rejetée', default => 'En attente' };
    $urgenceLabel = ['normale'=>'Normale','urgent'=>'Urgent','flexible'=>'Flexible'][$d['urgence'] ?? 'normale'];
?>
<div class="card">
    <div class="card-body">
        <div class="card-meta">
            <?php if (!empty($d['date_creation'])): ?><span><?= htmlspecialchars($d['date_creation']) ?></span><?php endif; ?>
            <?php if (!empty($d['competence_souhaitee'])): ?><span><?= htmlspecialchars($d['competence_souhaitee']) ?></span><?php endif; ?>
            <span><?= $urgenceLabel ?></span>
        </div>
        <h3><?= htmlspecialchars($d['titre']) ?></h3>
        <p><?= htmlspecialchars($d['description']) ?></p>
        <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
    </div>
    <div class="card-actions">
        <?php if ($statut !== 'approuve'): ?>
            <a href="?approuver=<?= $d['id'] ?>" class="btn btn-success btn-sm">Approuver</a>
        <?php else: ?>
            <span class="btn btn-disabled btn-sm">Approuvée</span>
        <?php endif; ?>

        <?php if ($statut !== 'rejete'): ?>
            <a href="?rejeter=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Rejeter cette demande ?')">Rejeter</a>
        <?php else: ?>
            <span class="btn btn-disabled btn-sm">Rejetée</span>
        <?php endif; ?>

        <a href="<?= $base ?>/view/admin/update_demande_admin.php?id=<?= $d['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
        <a href="?supprimer=<?= $d['id'] ?>" class="btn btn-icon btn-danger btn-sm" onclick="return confirm('Supprimer définitivement ?')" title="Supprimer">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </a>
    </div>
</div>
<?php endforeach; endif; ?>

</div></div></body></html>
