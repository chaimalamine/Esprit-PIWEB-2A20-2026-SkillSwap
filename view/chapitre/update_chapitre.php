<?php
include __DIR__ . '/../../controller/chapitreC.php';
include __DIR__ . '/../../model/chapitre.php';
include __DIR__ . '/../../controller/coursC.php';
$base = '/SkillSwap';
$chC  = new ChapitreC();
$cc   = new CoursC();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $fichier_pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = time() . '_' . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
        $fichier_pdf = $filename;
    }
    $ch = new Chapitre((int)$_POST['cours_id'], $_POST['titre'], $_POST['contenu'], $fichier_pdf);
    $chC->updateChapitre($ch, (int)$_POST['id']);
    header('Location: ' . $base . '/view/cours/detail_cours.php?id=' . $_POST['cours_id']);
    exit;
}

if (!isset($_GET['id'])) { die('ID manquant'); }
$chapitre = $chC->getChapitre((int)$_GET['id']);
if (!$chapitre) { die('Chapitre introuvable'); }
$cours = $cc->getCours((int)$chapitre['cours_id']);

include __DIR__ . '/../layout_shared.php';
?>

<div class="page-header">
    <div class="header-row">
        <div>
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $chapitre['cours_id'] ?>" style="color:#9ca3af;text-decoration:none;font-size:13px;font-weight:500;display:inline-block;margin-bottom:8px;">← Retour au cours</a>
            <h1>Modifier le chapitre</h1>
            <p>Cours : <strong style="color:#7b2ff7;"><?= htmlspecialchars($cours['titre'] ?? '') ?></strong></p>
        </div>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Modifier le chapitre</h2>
        <p class="form-subtitle">Mettez à jour le contenu de ce chapitre.</p>

        <form id="chapitreForm" method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $chapitre['id'] ?>">
            <input type="hidden" name="cours_id" value="<?= $chapitre['cours_id'] ?>">

            <div class="form-group">
                <label for="titre">Titre du chapitre <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($chapitre['titre']) ?>">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu <span class="req">*</span></label>
                <textarea id="contenu" name="contenu"><?= htmlspecialchars($chapitre['contenu']) ?></textarea>
                <span id="contenuMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="pdf">Nouveau fichier PDF <span style="color:#9ca3af;font-weight:400;">(optionnel — remplace l'ancien)</span></label>
                <?php if (!empty($chapitre['fichier_pdf'])): ?>
                <p style="font-size:12px;color:#9ca3af;margin-bottom:6px;">PDF actuel : <a href="<?= $base ?>/uploads/<?= htmlspecialchars($chapitre['fichier_pdf']) ?>" target="_blank" style="color:#7b2ff7;"><?= htmlspecialchars($chapitre['fichier_pdf']) ?></a></p>
                <?php endif; ?>
                <input type="file" id="pdf" name="pdf" accept=".pdf" style="padding:9px 14px;background:#fdfcff;color:#555;">
                <span id="pdfMsg" class="field-msg"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Enregistrer les modifications</button>
                <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $chapitre['cours_id'] ?>" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>Modifier un chapitre</h3>
        <p>Vous pouvez modifier le titre, le contenu et remplacer le PDF.</p>
        <ul>
            <li>Les modifications sont immédiates</li>
            <li>Le PDF n'est remplacé que si vous en envoyez un nouveau</li>
            <li>L'ordre des chapitres reste inchangé</li>
        </ul>
        <div class="info-notice">
            Ce chapitre appartient au cours <strong>"<?= htmlspecialchars($cours['titre'] ?? '') ?>"</strong>.
        </div>
    </div>
</div>

</div></body></html>
