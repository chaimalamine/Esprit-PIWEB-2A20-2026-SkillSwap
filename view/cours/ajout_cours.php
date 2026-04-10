<?php
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>
    <h1>Proposer un cours</h1>
    <div class="form-card">
        <form action="<?= $base ?>/view/cours/add_cours.php" method="POST">
            <input type="text" name="titre" placeholder="Titre du cours">
            <textarea name="description" placeholder="Description du cours"></textarea>
            <button type="submit" class="btn btn-purple">Enregistrer</button>
            <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
