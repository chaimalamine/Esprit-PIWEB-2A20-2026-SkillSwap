<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../controller/chapitreC.php';
$base = '/SkillSwap';

$cc = new CoursC();
$chC = new ChapitreC();
$cours = $cc->getCours($_GET['id']);
$chapitres = $chC->listeChapitres($_GET['id']);
if (!$cours) { die('Cours introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>
    <a href="<?= $base ?>/view/cours/liste_cours.php" style="color:#7b2ff7;text-decoration:none;font-size:14px">← Retour aux cours</a>
    <h1 style="margin-top:16px"><?= htmlspecialchars($cours['titre']) ?></h1>
    <p style="color:#555;margin-bottom:24px"><?= htmlspecialchars($cours['description']) ?></p>

    <a href="<?= $base ?>/view/chapitre/ajout_chapitre.php?cours_id=<?= $cours['id'] ?>" class="btn btn-purple" style="margin-bottom:24px">+ Ajouter un chapitre</a>
    <h2 style="margin:24px 0 12px;color:#333;font-size:20px">Chapitres</h2>

    <?php foreach ($chapitres as $ch): ?>
    <div class="card">
        <h3><?= htmlspecialchars($ch['titre']) ?></h3>
        <p><?= htmlspecialchars($ch['contenu']) ?></p>
        <div class="actions">
            <?php if (!empty($ch['fichier_pdf'])): ?>
                <a href="<?= $base ?>/uploads/<?= $ch['fichier_pdf'] ?>" target="_blank" class="btn btn-gray">Voir PDF</a>
            <?php endif; ?>
            <a href="<?= $base ?>/view/chapitre/update_chapitre.php?id=<?= $ch['id'] ?>" class="btn btn-gray">Modifier</a>
            <a href="<?= $base ?>/view/chapitre/delete_chapitre.php?id=<?= $ch['id'] ?>&cours_id=<?= $cours['id'] ?>" class="btn btn-red" onclick="return confirm('Supprimer ?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($chapitres)): ?>
        <p class="empty">Aucun chapitre pour ce cours.</p>
    <?php endif; ?>
</div></body></html>
