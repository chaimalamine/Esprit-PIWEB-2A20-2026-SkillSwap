<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc = new DemandeC();
$d = new Demande($_POST['titre'], $_POST['description']);
$dc->addDemande($d);
header('Location: ' . $base . '/view/demande/liste_demande.php');
exit;
