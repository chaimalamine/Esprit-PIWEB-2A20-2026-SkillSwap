<?php
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>

<!-- Stepper -->
<div style="display:flex;align-items:center;gap:0;margin-bottom:32px;background:white;border-radius:14px;padding:20px 28px;border:1px solid #f0eaff;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <!-- Étape 1 — en cours -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7b2ff7,#a855f7);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 0 4px #ede9ff;">
            <span style="color:white;font-size:13px;font-weight:700;">1</span>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 1</div>
            <div style="font-size:13px;font-weight:600;color:#1a0533;">Informations du cours</div>
        </div>
    </div>
    <div style="flex:1;height:2px;background:#f0eaff;margin:0 16px;"></div>
    <!-- Étape 2 -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#f5f3ff;border:2px solid #e9d5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="color:#c4b5fd;font-size:13px;font-weight:700;">2</span>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 2</div>
            <div style="font-size:13px;font-weight:500;color:#9ca3af;">Chapitres</div>
        </div>
    </div>
    <div style="flex:1;height:2px;background:#f0eaff;margin:0 16px;"></div>
    <!-- Étape 3 -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#f5f3ff;border:2px solid #e9d5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="color:#c4b5fd;font-size:13px;font-weight:700;">3</span>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 3</div>
            <div style="font-size:13px;font-weight:500;color:#9ca3af;">Soumettre</div>
        </div>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Informations du cours</h2>
        <span class="form-subtitle">Remplissez les détails de votre cours. Vous ajouterez les chapitres à l'étape suivante.</span>

        <form id="coursForm" action="<?= $base ?>/view/cours/add_cours.php" method="POST">

            <div class="form-group">
                <label for="titre">Titre du cours <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Initiation au développement Python">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" placeholder="Décrivez le contenu, les objectifs et le public cible de votre cours..."></textarea>
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
                        <option value="debutant">Débutant</option>
                        <option value="intermediaire">Intermédiaire</option>
                        <option value="avance">Avancé</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Continuer → Ajouter les chapitres</button>
                <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>Comment ça marche ?</h3>
        <p>Créez votre cours en 3 étapes simples.</p>
        <ul>
            <li>Étape 1 — Remplissez les infos du cours</li>
            <li>Étape 2 — Ajoutez vos chapitres (autant que vous voulez)</li>
            <li>Étape 3 — Soumettez pour validation</li>
        </ul>
        <div class="info-notice">
            Votre cours ne sera pas publié immédiatement. L'administrateur l'examinera avant de l'approuver.
        </div>
    </div>
</div>

</div></body></html>
