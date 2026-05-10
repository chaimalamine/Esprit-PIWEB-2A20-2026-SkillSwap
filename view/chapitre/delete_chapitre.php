<?php
include __DIR__ . '/../../controller/chapitreC.php';
$base = '/SkillSwap';
$chC = new ChapitreC();
$chC->deleteChapitre($_GET['id']);
header('Location: ' . $base . '/view/cours/detail_cours.php?id=' . $_GET['cours_id']);
exit;
