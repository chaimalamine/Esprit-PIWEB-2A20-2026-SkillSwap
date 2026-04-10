<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';

$cc = new CoursC();

if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['description'])) {
    $c = new Cours($_POST['titre'], $_POST['description']);
    $cc->updateCours($c, $_POST['id']);
    header('Location: ' . $base . '/view/cours/liste_cours.php');
    exit;
}

if (isset($_GET['id'])) {
    $cours = $cc->getCours($_GET['id']);
}
if (!isset($cours) || !$cours) { die('Cours introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>
    <h1>Modifier le cours</h1>
    <div class="form-card">
        <form method="POST" action="<?= $base ?>/view/cours/update_cours.php">
            <input type="hidden" name="id" value="<?= $cours['id'] ?>">
            <input type="text" name="titre" value="<?= htmlspecialchars($cours['titre']) ?>">
            <textarea name="description"><?= htmlspecialchars($cours['description']) ?></textarea>
            <button type="submit" class="btn btn-purple">Modifier</button>
            <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-gray">Annuler</a>
        </form>
    </div>
</div></body></html>
