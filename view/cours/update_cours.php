<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';
$cc   = new CoursC();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $existing = $cc->getCours((int)$_POST['id']);
    $c = new Cours(
        $_POST['titre'],
        $_POST['description'],
        $_POST['categorie']     ?? '',
        $_POST['niveau']        ?? 'debutant',
        $_POST['date_creation'] ?? date('Y-m-d H:i:s'),
        $existing['statut']     ?? 'en_attente'
    );
    $cc->updateCours($c, (int)$_POST['id']);
    header('Location: ' . $base . '/view/cours/liste_cours.php');
    exit;
}

if (!isset($_GET['id'])) { die('ID manquant'); }
$cours = $cc->getCours((int)$_GET['id']);
if (!$cours) { die('Cours introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>

<div class="page-header">
    <div class="header-row">
        <div>
            <h1>Modifier le cours</h1>
            <p>Mettez à jour les informations de votre cours</p>
        </div>
        <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-gray btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Modifier les détails</h2>
        <?php
        $statut = $cours['statut'] ?? 'en_attente';
        $badgeClass = match($statut) { 'approuve' => 'badge-approved', 'rejete' => 'badge-rejected', default => 'badge-pending' };
        $badgeLabel = match($statut) { 'approuve' => '✅ Approuvé', 'rejete' => '❌ Rejeté', default => '⏳ En attente' };
        ?>
        <p class="form-subtitle">Statut actuel : <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></p>

        <form id="coursForm" method="POST" action="">
            <input type="hidden" name="id" value="<?= $cours['id'] ?>">
            <input type="hidden" name="date_creation" value="<?= htmlspecialchars($cours['date_creation'] ?? '') ?>">

            <div class="form-group">
                <label for="titre">Titre du cours <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($cours['titre']) ?>">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description"><?= htmlspecialchars($cours['description']) ?></textarea>
                <span id="descMsg" class="field-msg"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categorie">Catégorie</label>
                    <input type="text" id="categorie" name="categorie" value="<?= htmlspecialchars($cours['categorie'] ?? '') ?>" placeholder="Ex : Développement…">
                    <span id="categorieMsg" class="field-msg"></span>
                </div>
                <div class="form-group">
                    <label for="niveau">Niveau</label>
                    <select id="niveau" name="niveau">
                        <?php foreach (['debutant'=>'🟢 Débutant','intermediaire'=>'🟡 Intermédiaire','avance'=>'🔴 Avancé'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($cours['niveau'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Enregistrer les modifications</button>
                <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>ℹ️ Informations</h3>
        <p>La modification de votre cours ne change pas son statut de validation.</p>
        <ul>
            <li>✏️ Modifiez les informations librement</li>
            <li>🔒 Le statut reste inchangé</li>
            <li>👁️ Seuls les cours approuvés sont visibles</li>
        </ul>
        <div class="info-notice">
            💡 Si votre cours a été rejeté, modifiez-le et contactez l'administrateur pour une nouvelle validation.
        </div>
    </div>
</div>

</div></body></html>
