<?php
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>
    <h1>Ajouter un chapitre</h1>
    <div class="form-card">
        <form action="<?= $base ?>/view/chapitre/add_chapitre.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="cours_id" value="<?= $_GET['cours_id'] ?>">
            <input type="text" name="titre" placeholder="Titre du chapitre">
            <textarea name="contenu" placeholder="Contenu du chapitre"></textarea>
            <label style="font-size:13px;color:#666;display:block;margin-bottom:8px">Fichier PDF (optionnel)</label>
            <input type="file" name="pdf" accept=".pdf" style="margin-bottom:16px">
            <button type="submit" class="btn btn-purple">Enregistrer</button>
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $_GET['cours_id'] ?>" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
