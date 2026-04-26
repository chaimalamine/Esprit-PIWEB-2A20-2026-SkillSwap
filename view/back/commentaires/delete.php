<?php
require_once __DIR__ . '/../../../controller/commentaireC.php';

// Vérifier si l'ID est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Vérifier si la confirmation a été donnée
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    session_start();
    $cc = new commentaireC();
    $cc->deleteCommentaire($id);
    $_SESSION['success'] = "Commentaire supprimé avec succès";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de suppression</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef2f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .modal-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .modal-container h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .modal-container p {
            color: #666;
            margin-bottom: 20px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-confirm {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-confirm:hover {
            background: #cc0000;
        }
        .btn-cancel {
            background: #ccc;
            color: #333;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cancel:hover {
            background: #aaa;
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <h3>Confirmation de suppression</h3>
        <p>Voulez-vous vraiment supprimer ce commentaire ?</p>
        <div class="modal-actions">
            <a href="?id=<?= $id ?>&confirm=yes" class="btn-confirm">Oui, supprimer</a>
            <a href="index.php" class="btn-cancel">Annuler</a>
        </div>
    </div>
</body>
</html>