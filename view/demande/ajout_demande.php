<?php
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>
    <h1>Ajouter une demande</h1>
    <div class="form-card">
        <form action="<?= $base ?>/view/demande/add_demande.php" method="POST">
            <input type="text" name="titre" placeholder="Titre de la demande">
            <textarea name="description" placeholder="Décrivez votre demande..."></textarea>
            <button type="submit" class="btn btn-purple">Enregistrer</button>
            <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
