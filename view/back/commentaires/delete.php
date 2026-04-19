<?php
require_once __DIR__ . '/../../../controller/commentaireC.php';

session_start();
$cc = new commentaireC();
$cc->deleteCommentaire($_GET['id']);
$_SESSION['success'] = "Commentaire supprimé avec succès";
header('Location: index.php');
exit();
?>