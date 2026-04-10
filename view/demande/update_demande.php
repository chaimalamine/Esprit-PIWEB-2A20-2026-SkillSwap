<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc = new DemandeC();

if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['description'])) {
    $d = new Demande($_POST['titre'], $_POST['description']);
    $dc->updateDemande($d, $_POST['id']);
    header('Location: ' . $base . '/view/demande/liste_demande.php');
    exit;
}

if (isset($_GET['id'])) { $demande = $dc->getDemande($_GET['id']); }
if (!isset($demande) || !$demande) { die('Demande introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>
    <h1>Modifier la demande</h1>
    <div class="form-card">
        <form method="POST" action="<?= $base ?>/view/demande/update_demande.php">
            <input type="hidden" name="id" value="<?= $demande['id'] ?>">
            <input type="text" name="titre" value="<?= htmlspecialchars($demande['titre']) ?>">
            <textarea name="description"><?= htmlspecialchars($demande['description']) ?></textarea>
            <button type="submit" class="btn btn-purple">Modifier</button>
            <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
