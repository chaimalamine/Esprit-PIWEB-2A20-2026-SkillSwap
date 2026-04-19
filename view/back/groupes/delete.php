<?php
require_once __DIR__ . '/../../../controller/groupeC.php';

session_start();
$gc = new groupeC();
$gc->deleteGroupe($_GET['id']);
$_SESSION['success'] = "Groupe supprimé avec succès";
header('Location: index.php');
exit();
?>