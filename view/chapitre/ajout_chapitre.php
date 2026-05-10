<?php
$base = '/SkillSwap';
$cours_id = (int)($_GET['cours_id'] ?? 0);
if (!$cours_id) { header('Location: '.$base.'/view/cours/liste_cours.php'); exit; }

// Récupérer le cours pour afficher son titre
include __DIR__ . '/../../controller/coursC.php';
$cc    = new CoursC();
$cours = $cc->getCours($cours_id);
if (!$cours) { die('Cours introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>

<div class="page-header">
    <div class="header-row">
        <div>
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $cours_id ?>" style="color:#9ca3af;text-decoration:none;font-size:13px;font-weight:500;display:inline-block;margin-bottom:8px;">← Retour au cours</a>
            <h1>Ajouter un chapitre</h1>
            <p>Cours : <strong style="color:#7b2ff7;"><?= htmlspecialchars($cours['titre']) ?></strong></p>
        </div>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Nouveau chapitre</h2>
        <p class="form-subtitle">Ajoutez un chapitre pour structurer votre cours.</p>

        <form id="chapitreForm" action="<?= $base ?>/view/chapitre/add_chapitre.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="cours_id" value="<?= $cours_id ?>">

            <div class="form-group">
                <label for="titre">Titre du chapitre <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Introduction aux variables">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu <span class="req">*</span></label>
                <textarea id="contenu" name="contenu" placeholder="Décrivez le contenu de ce chapitre..."></textarea>
                <span id="contenuMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="pdf">Fichier PDF <span style="color:#9ca3af;font-weight:400;">(optionnel)</span></label>
                <input type="file" id="pdf" name="pdf" accept=".pdf" style="padding:9px 14px;background:#fdfcff;color:#555;">
                <span id="pdfMsg" class="field-msg"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Enregistrer le chapitre</button>
                <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $cours_id ?>" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>Structure du cours</h3>
        <p>Un bon cours est bien structuré en chapitres progressifs.</p>
        <ul>
            <li>Commencez par une introduction</li>
            <li>Progressez du simple au complexe</li>
            <li>Ajoutez un PDF si nécessaire</li>
            <li>Vous pouvez ajouter autant de chapitres que souhaité</li>
        </ul>
        <div class="info-notice">
            Ce chapitre sera ajouté au cours <strong>"<?= htmlspecialchars($cours['titre']) ?>"</strong> et visible uniquement si le cours est approuvé.
        </div>
    </div>
</div>

</div></body></html>
