<?php
include __DIR__ . '/../../controller/coursC.php';
$cc   = new CoursC();
$base = '/SkillSwap';

if (isset($_GET['approuver']))  { $cc->updateStatutCours((int)$_GET['approuver'], 'approuve'); header('Location: '.$base.'/view/admin/liste_cours_admin.php'); exit; }
if (isset($_GET['rejeter']))    { $cc->updateStatutCours((int)$_GET['rejeter'],   'rejete');   header('Location: '.$base.'/view/admin/liste_cours_admin.php'); exit; }
if (isset($_GET['supprimer']))  { $cc->deleteCours((int)$_GET['supprimer']);                  header('Location: '.$base.'/view/admin/liste_cours_admin.php'); exit; }

$liste = $cc->listeCours();
include __DIR__ . '/layout_admin.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="color:#1a0533;font-size:22px;font-weight:700;margin-bottom:3px;">Cours</h1>
        <p style="color:#9ca3af;font-size:13px;">Modérez et gérez tous les cours soumis</p>
    </div>
    <a href="<?= $base ?>/view/admin/ajout_cours_admin.php" class="btn btn-primary">Ajouter un cours</a>
</div>

<?php $rows = $liste->fetchAll();
if (empty($rows)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucun cours</h3>
        <p>Les cours soumis par les utilisateurs apparaîtront ici.</p>
    </div>
<?php else: foreach ($rows as $c):
    $statut     = $c['statut'] ?? 'en_attente';
    $badgeClass = match($statut) { 'approuve' => 'badge-approved', 'rejete' => 'badge-rejected', default => 'badge-pending' };
    $badgeLabel = match($statut) { 'approuve' => 'Approuvé', 'rejete' => 'Rejeté', default => 'En attente' };
    $niveauLabel = ['debutant'=>'Débutant','intermediaire'=>'Intermédiaire','avance'=>'Avancé'][$c['niveau'] ?? 'debutant'];
?>
<div class="card">
    <div class="card-body">
        <div class="card-meta">
            <?php if (!empty($c['date_creation'])): ?><span><?= htmlspecialchars($c['date_creation']) ?></span><?php endif; ?>
            <?php if (!empty($c['categorie'])): ?><span><?= htmlspecialchars($c['categorie']) ?></span><?php endif; ?>
            <span><?= $niveauLabel ?></span>
        </div>
        <h3><?= htmlspecialchars($c['titre']) ?></h3>
        <p><?= htmlspecialchars($c['description']) ?></p>
        <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
    </div>
    <div class="card-actions">
        <?php if ($statut !== 'approuve'): ?>
            <a href="?approuver=<?= $c['id'] ?>" class="btn btn-success btn-sm">Approuver</a>
        <?php else: ?>
            <span class="btn btn-disabled btn-sm">Approuvé</span>
        <?php endif; ?>

        <?php if ($statut !== 'rejete'): ?>
            <a href="?rejeter=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Rejeter ce cours ?')">Rejeter</a>
        <?php else: ?>
            <span class="btn btn-disabled btn-sm">Rejeté</span>
        <?php endif; ?>

        <a href="<?= $base ?>/view/admin/update_cours_admin.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
        <a href="?supprimer=<?= $c['id'] ?>" class="btn btn-icon btn-danger btn-sm" onclick="return confirm('Supprimer définitivement ?')" title="Supprimer">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </a>
    </div>
</div>
<?php endforeach; endif; ?>

</div></div></body></html>
