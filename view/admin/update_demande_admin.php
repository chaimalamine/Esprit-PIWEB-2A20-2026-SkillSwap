<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc   = new DemandeC();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $d = new Demande(
        $_POST['titre'],
        $_POST['description'],
        $_POST['competence_souhaitee'] ?? '',
        $_POST['urgence']              ?? 'normale',
        $_POST['date_creation']        ?? date('Y-m-d H:i:s'),
        $_POST['statut']               ?? 'en_attente'
    );
    $dc->updateDemande($d, (int)$_POST['id']);
    header('Location: ' . $base . '/view/admin/liste_demande_admin.php'); exit;
}

if (!isset($_GET['id'])) { die('ID manquant'); }
$demande = $dc->getDemande((int)$_GET['id']);
if (!$demande) { die('Demande introuvable'); }

include __DIR__ . '/layout_admin.php';
?>

<div style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h1 style="color:#1a0533;margin:0;">Modifier la demande</h1>
            <p style="color:#999;font-size:14px;margin-top:4px;">Modifiez les informations et le statut de la demande</p>
        </div>
        <a href="<?= $base ?>/view/admin/liste_demande_admin.php" class="btn btn-ghost btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Détails de la demande</h2>
        <?php
        $statut     = $demande['statut'] ?? 'en_attente';
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
                    <input type="text" id="competence_souhaitee" name="competence_souhaitee" value="<?= htmlspecialchars($demande['competence_souhaitee'] ?? '') ?>" placeholder="Ex : Guitare…">
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

            <div class="form-group">
                <label for="statut">Statut de publication</label>
                <select id="statut" name="statut">
                    <option value="approuve"   <?= $statut === 'approuve'   ? 'selected' : '' ?>>✅ Approuvée (Publiée)</option>
                    <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                    <option value="rejete"     <?= $statut === 'rejete'     ? 'selected' : '' ?>>❌ Rejetée</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?= $base ?>/view/admin/liste_demande_admin.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>🔧 Contrôle Admin</h3>
        <p>Vous pouvez modifier le contenu et changer le statut de publication.</p>
        <ul>
            <li>✅ Approuvée → visible sur le site</li>
            <li>⏳ En attente → masquée du public</li>
            <li>❌ Rejetée → masquée du public</li>
        </ul>
        <div class="info-notice">
            ⚠️ Changer le statut vers <strong>En attente</strong> ou <strong>Rejetée</strong> masquera cette demande immédiatement.
        </div>
    </div>
</div>

</div></div></body></html>
