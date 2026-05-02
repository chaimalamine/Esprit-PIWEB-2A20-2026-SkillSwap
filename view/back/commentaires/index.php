<?php
require_once __DIR__ . '/../../../controller/commentaireC.php';

$cc = new commentaireC();
$commentaires = $cc->listCommentaires();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Commentaires</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; display: flex; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main { flex: 1; margin-left: 220px; padding: 20px; }
        .topbar { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .admin-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .admin-table th { background: #6a11cb; color: white; }
        .btn-edit { background: #2575fc; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; display: inline-block; }
        .btn-delete { background: #ef4444; color: white; padding: 5px 10px; border-radius: 5px; border: none; font-size: 12px; cursor: pointer; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
        }
        .modal-actions { margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
        .btn-confirm { background: #ef4444; color: white; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer; }
        .btn-cancel { background: #ccc; color: #333; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap Admin</h2>
    <a href="../dashboard.php"> Groupes & Communauté</a>
    <a href="../groupes/index.php"> Groupes</a>
    <a href="../posts/index.php"> Posts</a>
    <a href="index.php"> Commentaires</a>
</div>

<div class="main">
    <div class="topbar">
        <h3>Gestion des Commentaires</h3>
    </div>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <table class="admin-table">
        <thead>
            <tr><th>ID</th><th>Contenu</th><th>Post ID</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php if(!empty($commentaires)): ?>
                <?php foreach($commentaires as $commentaire): ?>
                <tr>
                    <td><?= $commentaire['idcom'] ?></td>
                    <td><?= htmlspecialchars(substr($commentaire['contenu'], 0, 50)) ?>...<\/a>
                    <td><?= $commentaire['idpost'] ?>\<a>
                    <td><?= $commentaire['datecom'] ?>\<a>
                    <td>
                        <a href="edit.php?id=<?= $commentaire['idcom'] ?>" class="btn-edit">Modifier</a>
                        <button class="btn-delete" onclick="openDeleteModal(<?= $commentaire['idcom'] ?>, '<?= htmlspecialchars(substr($commentaire['contenu'], 0, 30)) ?>...')">Supprimer</button>
                    </a>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucun commentaire trouvé</a></td>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODALE UNIQUE -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirmation de suppression</h3>
        <p id="modalMessage">Voulez-vous vraiment supprimer ce commentaire ?</p>
        <div class="modal-actions">
            <button class="btn-confirm" id="confirmDelete">Oui, supprimer</button>
            <button class="btn-cancel" onclick="closeDeleteModal()">Annuler</button>
        </div>
    </div>
</div>

<script>
    let deleteId = null;
    let deleteUrl = '';

    function openDeleteModal(id, contenu) {
        deleteId = id;
        deleteUrl = 'delete.php?id=' + id;
        document.getElementById('modalMessage').innerHTML = 'Voulez-vous vraiment supprimer le commentaire <strong>"' + contenu + '"</strong> ?';
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteId = null;
    }

    document.getElementById('confirmDelete').onclick = function() {
        if (deleteId) {
            window.location.href = deleteUrl;
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeDeleteModal();
        }
    }
</script>

</body>
</html>