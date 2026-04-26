<?php
include __DIR__ . '/../../controller/demandeC.php';
include __DIR__ . '/../../model/demande.php';
$base = '/SkillSwap';
$dc   = new DemandeC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = new Demande(
        $_POST['titre'],
        $_POST['description'],
        $_POST['competence_souhaitee'] ?? '',
        $_POST['urgence']              ?? 'normale',
        date('Y-m-d H:i:s'),
        $_POST['statut']               ?? 'approuve'
    );
    $dc->addDemande($d);
    header('Location: ' . $base . '/view/admin/liste_demande_admin.php'); exit;
}

include __DIR__ . '/layout_admin.php';
?>

<div style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h1 style="color:#1a0533;margin:0;">Ajouter une demande</h1>
            <p style="color:#999;font-size:14px;margin-top:4px;">Créez une nouvelle demande depuis l'administration</p>
        </div>
        <a href="<?= $base ?>/view/admin/liste_demande_admin.php" class="btn btn-ghost btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Détails de la demande</h2>
        <p class="form-subtitle">En tant qu'administrateur, vous pouvez publier directement.</p>

        <form id="demandeForm" method="POST" action="">

            <div class="form-group">
                <label for="titre">Titre de la demande <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Cherche un professeur de guitare">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" placeholder="Décrivez le besoin, le niveau, les disponibilités..."></textarea>
                <span id="descMsg" class="field-msg"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="competence_souhaitee">Compétence souhaitée</label>
                    <input type="text" id="competence_souhaitee" name="competence_souhaitee" placeholder="Ex : Guitare, Photoshop…">
                    <span id="competenceMsg" class="field-msg"></span>
                </div>
                <div class="form-group">
                    <label for="urgence">Urgence</label>
                    <select id="urgence" name="urgence">
                        <option value="normale">🟢 Normale</option>
                        <option value="urgent">🔴 Urgent</option>
                        <option value="flexible">🔵 Flexible</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="statut">Statut de publication</label>
                <select id="statut" name="statut">
                    <option value="approuve">✅ Publiée (Approuvée)</option>
                    <option value="en_attente">⏳ En attente</option>
                    <option value="rejete">❌ Rejetée</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer la demande</button>
                <a href="<?= $base ?>/view/admin/liste_demande_admin.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>⚡ Privilèges Admin</h3>
        <p>En tant qu'administrateur, vous contrôlez la publication des demandes.</p>
        <ul>
            <li>✅ Publiez directement sans attente</li>
            <li>⏳ Créez en brouillon si besoin</li>
            <li>🔒 Les utilisateurs passent par validation</li>
            <li>👁️ Seul l'admin peut changer le statut</li>
        </ul>
        <div class="info-notice">
            💡 Choisissez <strong>Publiée</strong> pour que la demande soit visible immédiatement.
        </div>
    </div>
</div>

</div></div></body></html>
