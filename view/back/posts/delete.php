<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    require_once __DIR__ . '/../../../controller/postC.php';
    session_start();
    $pc = new postC();
    $pc->deletePost($id);
    $_SESSION['success'] = "Post supprimé avec succès";
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
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .modal-container { background: white; padding: 30px; border-radius: 15px; width: 400px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .modal-container h3 { margin-bottom: 15px; }
        .modal-container p { margin-bottom: 20px; color: #666; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }
        .btn-confirm { background: #ef4444; color: white; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer; text-decoration: none; }
        .btn-cancel { background: #ccc; color: #333; padding: 8px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="modal-container">
        <h3>Confirmation de suppression</h3>
        <p>Voulez-vous vraiment supprimer ce post ?</p>
        <div class="modal-actions">
            <a href="?id=<?= $id ?>&confirm=yes" class="btn-confirm">Oui, supprimer</a>
            <a href="index.php" class="btn-cancel">Annuler</a>
        </div>
    </div>
</body>
</html>