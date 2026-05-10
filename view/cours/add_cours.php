<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';
$cc   = new CoursC();

$c = new Cours(
    $_POST['titre'],
    $_POST['description'],
    $_POST['categorie'] ?? '',
    $_POST['niveau']    ?? 'debutant',
    date('Y-m-d H:i:s'),
    'en_attente'
);
$cc->addCours($c);

// Récupérer l'ID du cours qu'on vient d'insérer
$db     = config::getConnexion();
$new_id = (int)$db->lastInsertId();

// Rediriger vers la page d'ajout de chapitres
header('Location: ' . $base . '/view/cours/cours_chapitres.php?cours_id=' . $new_id);
exit;
