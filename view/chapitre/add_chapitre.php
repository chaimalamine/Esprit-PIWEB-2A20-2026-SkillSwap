<?php
include __DIR__ . '/../../controller/chapitreC.php';
include __DIR__ . '/../../model/chapitre.php';
$base = '/SkillSwap';
$fichier_pdf = null;
if (!empty($_FILES['pdf']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $filename = time() . '_' . basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
    $fichier_pdf = $filename;
}
$chC = new ChapitreC();
$ch = new Chapitre($_POST['cours_id'], $_POST['titre'], $_POST['contenu'], $fichier_pdf);
$chC->addChapitre($ch);
header('Location: ' . $base . '/view/cours/detail_cours.php?id=' . $_POST['cours_id']);
exit;
