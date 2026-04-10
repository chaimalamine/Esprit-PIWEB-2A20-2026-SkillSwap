<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';
$cc = new CoursC();
$c = new Cours($_POST['titre'], $_POST['description']);
$cc->addCours($c);
header('Location: ' . $base . '/view/cours/liste_cours.php');
exit;
