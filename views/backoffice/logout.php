<?php
session_start();
session_destroy();
header('Location: http://localhost/projetwebfinal/views/frontoffice/frontdesigns.php');
exit();
?>