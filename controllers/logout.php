<?php
session_start();
session_destroy();
header('Location: http://localhost/projetwebfinal/views/frontoffice/connexion.php');
exit();
?>