<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../model/cours.php';
$base = '/SkillSwap';
$cc   = new CoursC();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $c = new Cours(
        $_POST['titre'],
        $_POST['description'],
        $_POST['categorie']     ?? '',
        $_POST['niveau']        ?? 'debutant',
        $_POST['date_creation'] ?? date('Y-m-d H:i:s'),
        $_POST['statut']        ?? 'en_attente'
    );
    $cc->updateCours($c, (int)$_POST['id']);
    header('Location: ' . $base . '/view/admin/liste_cours_admin.php'); exit;
}

if (!isset($_GET['id'])) { die('ID manquant'); }
$cours = $cc->getCours((int)$_GET['id']);
if (!$cours) { die('Cours introuvable'); }

include __DIR__ . '/layout_admin.php';
?>

<div style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h1 style="color:#1a0533;margin:0;">Modifier le cours</h1>
            <p style="color:#999;font-size:14px;margin-top:4px;">Modifiez les informations et le statut du cours</p>
        </div>
        <a href="<?= $base ?>/view/admin/liste_cours_admin.php" class="btn btn-ghost btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Détails du cours</h2>
        <?php
        $statut     = $cours['statut'] ?? 'en_attente';
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

            <div class="form-group">
                <label for="statut">Statut de publication</label>
                <select id="statut" name="statut">
                    <option value="approuve"   <?= $statut === 'approuve'   ? 'selected' : '' ?>>✅ Approuvé (Publié)</option>
                    <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                    <option value="rejete"     <?= $statut === 'rejete'     ? 'selected' : '' ?>>❌ Rejeté</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?= $base ?>/view/admin/liste_cours_admin.php" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>🔧 Contrôle Admin</h3>
        <p>Vous pouvez modifier le contenu et changer le statut de publication.</p>
        <ul>
            <li>✅ Approuvé → visible sur le site</li>
            <li>⏳ En attente → masqué du public</li>
            <li>❌ Rejeté → masqué du public</li>
        </ul>
        <div class="info-notice">
            ⚠️ Changer le statut vers <strong>En attente</strong> ou <strong>Rejeté</strong> masquera ce cours du site public immédiatement.
        </div>
    </div>
</div>

</div></div></body></html>
