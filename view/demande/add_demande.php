<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc = new DemandeC();
$d = new Demande(
    $_POST['titre'],
    $_POST['description'],
    $_POST['competence_souhaitee'] ?? '',
    $_POST['urgence']              ?? 'normale',
    date('Y-m-d H:i:s'),
    'en_attente'
);
$dc->addDemande($d);
header('Location: ' . $base . '/view/demande/liste_demande.php');
exit;
