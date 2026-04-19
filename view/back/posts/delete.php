<?php
require_once __DIR__ . '/../../../controller/postC.php';

session_start();
$pc = new postC();
$pc->deletePost($_GET['id']);
$_SESSION['success'] = "Post supprimé avec succès";
header('Location: index.php');
exit();
?>