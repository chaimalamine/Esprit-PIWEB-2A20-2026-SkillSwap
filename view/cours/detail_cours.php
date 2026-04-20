<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../controller/chapitreC.php';
$base = '/SkillSwap';
$cc   = new CoursC();
$chC  = new ChapitreC();

if (!isset($_GET['id'])) { header('Location: '.$base.'/view/cours/liste_cours.php'); exit; }
$cours     = $cc->getCours((int)$_GET['id']);
if (!$cours) { die('Cours introuvable'); }
$chapitres = $chC->listeChapitres((int)$_GET['id'])->fetchAll();

include __DIR__ . '/../layout_shared.php';
?>

<div style="margin-bottom:18px;">
    <a href="<?= $base ?>/view/cours/liste_cours.php" style="color:#9ca3af;text-decoration:none;font-size:13px;font-weight:500;">← Retour aux cours</a>
</div>

<!-- En-tête cours -->
<div style="background:white;border-radius:20px;padding:36px 40px;margin-bottom:24px;border:1px solid #f0eaff;box-shadow:0 2px 12px rgba(123,47,247,0.05);">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <?php $niveauLabel = ['debutant'=>'Débutant','intermediaire'=>'Intermédiaire','avance'=>'Avancé'][$cours['niveau'] ?? 'debutant']; ?>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                <?php if (!empty($cours['categorie'])): ?>
                <span style="background:#f5f3ff;color:#7b2ff7;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;"><?= htmlspecialchars($cours['categorie']) ?></span>
                <?php endif; ?>
                <span style="background:#f5f3ff;color:#7b2ff7;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;"><?= $niveauLabel ?></span>
            </div>
            <h1 style="color:#1a0533;font-size:26px;font-weight:800;margin-bottom:12px;line-height:1.2;"><?= htmlspecialchars($cours['titre']) ?></h1>
            <p style="color:#6b7280;font-size:15px;line-height:1.65;"><?= htmlspecialchars($cours['description']) ?></p>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;align-items:flex-end;flex-shrink:0;">
            <a href="<?= $base ?>/view/cours/cours_chapitres.php?cours_id=<?= $cours['id'] ?>" class="btn btn-purple">
                + Ajouter un chapitre
            </a>
            <a href="<?= $base ?>/view/cours/update_cours.php?id=<?= $cours['id'] ?>" class="btn btn-gray btn-sm">Modifier le cours</a>
        </div>
    </div>
    <?php if (!empty($cours['date_creation'])): ?>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f0eaff;font-size:12px;color:#9ca3af;">
        Publié le <?= htmlspecialchars($cours['date_creation']) ?> &nbsp;·&nbsp; <?= count($chapitres) ?> chapitre<?= count($chapitres) > 1 ? 's' : '' ?>
    </div>
    <?php endif; ?>
</div>

<!-- Chapitres -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <h2 style="color:#1a0533;font-size:18px;font-weight:700;">Chapitres</h2>
    <span style="background:#f5f3ff;color:#7b2ff7;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:600;"><?= count($chapitres) ?> chapitre<?= count($chapitres) > 1 ? 's' : '' ?></span>
</div>

<?php if (empty($chapitres)): ?>
<div class="empty-state">
    <div class="empty-icon">📄</div>
    <h3>Aucun chapitre</h3>
    <p>Ajoutez des chapitres pour structurer le contenu de ce cours.</p>
    <br>
    <a href="<?= $base ?>/view/cours/cours_chapitres.php?cours_id=<?= $cours['id'] ?>" class="btn btn-purple" style="margin-top:8px;">Ajouter des chapitres</a>
</div>
<?php else: ?>
<?php foreach ($chapitres as $i => $ch): ?>
<div class="card" style="display:flex;align-items:flex-start;gap:18px;">
    <div style="width:38px;height:38px;background:linear-gradient(135deg,#7b2ff7,#a855f7);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;"><?= $i + 1 ?></div>
    <div style="flex:1;min-width:0;">
        <h3 style="margin-bottom:6px;"><?= htmlspecialchars($ch['titre']) ?></h3>
        <p><?= htmlspecialchars($ch['contenu']) ?></p>
        <div class="actions">
            <?php if (!empty($ch['fichier_pdf'])): ?>
            <a href="<?= $base ?>/uploads/<?= htmlspecialchars($ch['fichier_pdf']) ?>" target="_blank" class="btn btn-gray btn-sm">Voir PDF</a>
            <?php endif; ?>
            <a href="<?= $base ?>/view/chapitre/update_chapitre.php?id=<?= $ch['id'] ?>" class="btn btn-gray btn-sm">Modifier</a>
            <a href="<?= $base ?>/view/chapitre/delete_chapitre.php?id=<?= $ch['id'] ?>&cours_id=<?= $cours['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Supprimer ce chapitre ?')">Supprimer</a>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

</div></body></html>
