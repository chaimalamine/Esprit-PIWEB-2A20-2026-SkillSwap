<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc   = new DemandeC();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $existing = $dc->getDemande((int)$_POST['id']);
    $d = new Demande(
        $_POST['titre'],
        $_POST['description'],
        $_POST['competence_souhaitee'] ?? '',
        $_POST['urgence']              ?? 'normale',
        $_POST['date_creation']        ?? date('Y-m-d H:i:s'),
        $existing['statut']            ?? 'en_attente'
    );
    $dc->updateDemande($d, (int)$_POST['id']);
    header('Location: ' . $base . '/view/demande/liste_demande.php');
    exit;
}

if (!isset($_GET['id'])) { die('ID manquant'); }
$demande = $dc->getDemande((int)$_GET['id']);
if (!$demande) { die('Demande introuvable'); }

include __DIR__ . '/../layout_shared.php';
?>

<div class="page-header">
    <div class="header-row">
        <div>
            <h1>Modifier la demande</h1>
            <p>Mettez à jour les informations de votre demande</p>
        </div>
        <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Modifier les détails</h2>
        <?php
        $statut = $demande['statut'] ?? 'en_attente';
        $badgeClass = match($statut) { 'approuve' => 'badge-approved', 'rejete' => 'badge-rejected', default => 'badge-pending' };
        $badgeLabel = match($statut) { 'approuve' => '✅ Approuvée', 'rejete' => '❌ Rejetée', default => '⏳ En attente' };
        ?>
        <p class="form-subtitle">Statut actuel : <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></p>

        <form id="demandeForm" method="POST" action="">
            <input type="hidden" name="id" value="<?= $demande['id'] ?>">
            <input type="hidden" name="date_creation" value="<?= htmlspecialchars($demande['date_creation'] ?? '') ?>">

            <div class="form-group">
                <label for="titre">Titre de la demande <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($demande['titre']) ?>">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description"><?= htmlspecialchars($demande['description']) ?></textarea>
                <span id="descMsg" class="field-msg"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="competence_souhaitee">Compétence souhaitée</label>
                    <input type="text" id="competence_souhaitee" name="competence_souhaitee" value="<?= htmlspecialchars($demande['competence_souhaitee'] ?? '') ?>" placeholder="Ex : Guitare, Photoshop…">
                    <span id="competenceMsg" class="field-msg"></span>
                </div>
                <div class="form-group">
                    <label for="urgence">Urgence</label>
                    <select id="urgence" name="urgence">
                        <?php foreach (['normale'=>'🟢 Normale','urgent'=>'🔴 Urgent','flexible'=>'🔵 Flexible'] as $v=>$l): ?>
                        <option value="<?= $v ?>" <?= ($demande['urgence'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Enregistrer les modifications</button>
                <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>ℹ️ Informations</h3>
        <p>La modification ne change pas le statut de validation de votre demande.</p>
        <ul>
            <li>✏️ Modifiez librement</li>
            <li>🔒 Le statut reste inchangé</li>
            <li>👁️ Seules les demandes approuvées sont visibles</li>
        </ul>
        <div class="info-notice">
            💡 Si votre demande a été rejetée, modifiez-la et contactez l'administrateur.
        </div>
    </div>
</div>

</div></body></html>
