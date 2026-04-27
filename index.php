<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/OffreController.php';
require_once __DIR__ . '/controllers/UserController.php';

$conn = getConnection();
$page = $_GET['page'] ?? 'frontoffice';
$action = $_POST['action'] ?? ($_GET['action'] ?? '');

switch ($page) {
    case 'backoffice':
        if ($action === 'export_pdf') {
            exporterOffresPdf($conn);
        }

        if ($action === 'store') {
            ajouterOffre($conn);
        }

        if ($action === 'update') {
            modifierOffre($conn);
        }

        if ($action === 'delete') {
            supprimerOffre($conn);
        }

        afficherBackoffice($conn);
        break;

    case 'inscription':
        if ($action === 'register') {
            inscrireUtilisateur($conn);
            break;
        }

        afficherInscription($conn);
        break;

    case 'frontoffice':
    default:
        afficherFrontoffice($conn);
        break;
}
