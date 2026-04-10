<?php
include __DIR__ . '/../../controller/coursC.php';
$base = '/SkillSwap';
$cc = new CoursC();
$cc->deleteCours($_GET['id']);
header('Location: ' . $base . '/view/cours/liste_cours.php');
exit;
