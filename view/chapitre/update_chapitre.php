<?php
include __DIR__ . '/../../controller/chapitreC.php';
include __DIR__ . '/../../model/chapitre.php';
$base = '/SkillSwap';
$chC = new ChapitreC();

if (isset($_POST['id'])) {
    $fichier_pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = time() . '_' . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
        $fichier_pdf = $filename;
    }
    $ch = new Chapitre($_POST['cours_id'], $_POST['titre'], $_POST['contenu'], $fichier_pdf);
    $chC->updateChapitre($ch, $_POST['id']);
    header('Location: ' . $base . '/view/cours/detail_cours.php?id=' . $_POST['cours_id']);
    exit;
}

if (isset($_GET['id'])) { $chapitre = $chC->getChapitre($_GET['id']); }
if (!isset($chapitre) || !$chapitre) { die('Chapitre introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>
    <h1>Modifier le chapitre</h1>
    <div class="form-card">
        <form method="POST" action="<?= $base ?>/view/chapitre/update_chapitre.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $chapitre['id'] ?>">
            <input type="hidden" name="cours_id" value="<?= $chapitre['cours_id'] ?>">
            <input type="text" name="titre" value="<?= htmlspecialchars($chapitre['titre']) ?>">
            <textarea name="contenu"><?= htmlspecialchars($chapitre['contenu']) ?></textarea>
            <label style="font-size:13px;color:#666;display:block;margin-bottom:8px">Nouveau PDF (optionnel)</label>
            <input type="file" name="pdf" accept=".pdf" style="margin-bottom:16px">
            <button type="submit" class="btn btn-purple">Modifier</button>
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $chapitre['cours_id'] ?>" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
