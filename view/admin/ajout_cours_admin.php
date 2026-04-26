<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';
$cc   = new CoursC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c = new Cours(
        $_POST['titre'],
        $_POST['description'],
        $_POST['categorie'] ?? '',
        $_POST['niveau']    ?? 'debutant',
        date('Y-m-d H:i:s'),
        $_POST['statut']    ?? 'approuve'
    );
    $cc->addCours($c);
    header('Location: ' . $base . '/view/admin/liste_cours_admin.php'); exit;
}

include __DIR__ . '/layout_admin.php';
?>

<div style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h1 style="color:#1a0533;margin:0;">Ajouter un cours</h1>
            <p style="color:#999;font-size:14px;margin-top:4px;">Créez un nouveau cours directement depuis l'administration</p>
        </div>
        <a href="<?= $base ?>/view/admin/liste_cours_admin.php" class="btn btn-ghost btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Détails du cours</h2>
        <p class="form-subtitle">En tant qu'administrateur, vous pouvez publier directement.</p>

        <form id="coursForm" method="POST" action="">

            <div class="form-group">
                <label for="titre">Titre du cours <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Initiation au développement Python">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" placeholder="Décrivez le contenu, les objectifs et le public cible..."></textarea>
                <span id="descMsg" class="field-msg"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categorie">Catégorie</label>
                    <input type="text" id="categorie" name="categorie" placeholder="Ex : Développement, Design…">
                    <span id="categorieMsg" class="field-msg"></span>
                </div>
                <div class="form-group">
                    <label for="niveau">Niveau</label>
                    <select id="niveau" name="niveau">
                        <option value="debutant">🟢 Débutant</option>
                        <option value="intermediaire">🟡 Intermédiaire</option>
                        <option value="avance">🔴 Avancé</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="statut">Statut de publication</label>
                <select id="statut" name="statut">
                    <option value="approuve">✅ Publié (Approuvé)</option>
                    <option value="en_attente">⏳ En attente</option>
                    <option value="rejete">❌ Rejeté</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer le cours</button>
                <a href="<?= $base ?>/view/admin/liste_cours_admin.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>⚡ Privilèges Admin</h3>
        <p>En tant qu'administrateur, vous contrôlez entièrement la publication.</p>
        <ul>
            <li>✅ Publiez directement sans attente</li>
            <li>⏳ Créez en brouillon si besoin</li>
            <li>🔒 Les utilisateurs passent par validation</li>
            <li>👁️ Seul l'admin peut changer le statut</li>
        </ul>
        <div class="info-notice">
            💡 Choisissez <strong>Publié</strong> pour que le cours soit visible immédiatement sur le site.
        </div>
    </div>
</div>

</div></div></body></html>
