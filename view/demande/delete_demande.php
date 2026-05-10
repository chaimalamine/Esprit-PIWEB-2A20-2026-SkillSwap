<?php
include __DIR__ . '/../../controller/demandeC.php';
$base = '/SkillSwap';
$dc = new DemandeC();
$dc->deleteDemande($_GET['id']);
header('Location: ' . $base . '/view/demande/liste_demande.php');
exit;
