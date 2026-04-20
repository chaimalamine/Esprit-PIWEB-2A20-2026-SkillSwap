<?php
$base = '/SkillSwap';
include __DIR__ . '/../layout_shared.php';
?>

<div class="page-header">
    <div class="header-row">
        <div>
            <h1>Soumettre une demande</h1>
            <p>Trouvez quelqu'un qui correspond à vos besoins</p>
        </div>
        <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray btn-sm">← Retour</a>
    </div>
</div>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Détails de la demande</h2>
        <p class="form-subtitle">Décrivez votre besoin. Votre demande sera visible après validation.</p>

        <form id="demandeForm" action="<?= $base ?>/view/demande/add_demande.php" method="POST">

            <div class="form-group">
                <label for="titre">Titre de la demande <span class="req">*</span></label>
                <input type="text" id="titre" name="titre" placeholder="Ex : Cherche un professeur de guitare">
                <span id="titreMsg" class="field-msg"></span>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" placeholder="Décrivez votre besoin, votre niveau actuel, vos disponibilités..."></textarea>
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

            <div class="form-actions">
                <button type="submit" class="btn btn-purple">Soumettre la demande</button>
                <a href="<?= $base ?>/view/demande/liste_demande.php" class="btn btn-gray">Annuler</a>
            </div>
        </form>
    </div>

    <div class="info-panel">
        <h3>🤝 Comment ça marche ?</h3>
        <p>Soumettez votre demande d'échange facilement.</p>
        <ul>
            <li>✏️ Décrivez votre besoin clairement</li>
            <li>📨 Votre demande est soumise à validation</li>
            <li>✅ Après approbation, elle est visible</li>
            <li>💬 Les membres peuvent vous contacter</li>
        </ul>
        <div class="info-notice">
            ⏳ <strong>En attente de validation</strong><br>
            Votre demande ne sera pas publiée immédiatement. L'administrateur l'examinera avant publication.
        </div>
    </div>
</div>

</div></body></html>
