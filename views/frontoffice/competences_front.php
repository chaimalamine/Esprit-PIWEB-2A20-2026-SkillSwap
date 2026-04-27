<?php
// ============================================================
// FICHIER INCLUS DANS FRONTDESIGN.PHP (pas de session_start ici)
// ============================================================

// --- VÉRIFICATION : utilisateur connecté ---
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// ============================================================
// TRAITEMENT DES ACTIONS (suppression, ajout, modification)
// ============================================================

// --- SUPPRESSION D'UNE COMPÉTENCE ---
// Déclenché par : clic sur 🗑️ dans le tableau
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];                             // ID de la compétence à supprimer
    $competenceC->supprimerCompetence($id);        // Appeler la fonction de suppression (définie dans frontdessign.php)
    echo '<script>window.location.href="frontdessign.php?page=competences";</script>';  // Rediriger via JavaScript
    exit();
}

// --- AJOUT MULTIPLE DE COMPÉTENCES ---
// Déclenché par : clic sur "Enregistrer tout"
// Reçoit des TABLEAUX [] car plusieurs lignes peuvent être soumises
if (isset($_POST['action']) && $_POST['action'] == 'ajouter_multiple') {
    $noms = $_POST['nom_competence'];              // Tableau des noms
    $niveaux = $_POST['niveau'];                   // Tableau des niveaux
    $categories = $_POST['categorie'];             // Tableau des catégories
    $heuresArr = $_POST['heures_echangees'];       // Tableau des heures
    $id_user = $_SESSION['user_id'];               // ID de l'utilisateur connecté
    
    $countAjoutes = 0;                             // Compteur de compétences ajoutées
    
    // Boucle pour traiter chaque ligne du formulaire
    for ($i = 0; $i < count($noms); $i++) {
        $nom = trim($noms[$i]);                    // Nettoyer le nom
        $niveau = $niveaux[$i];                    // Niveau de cette ligne
        $categorie = $categories[$i];              // Catégorie de cette ligne
        $heures = isset($heuresArr[$i]) ? $heuresArr[$i] : 0;  // Heures (0 par défaut)
        
        // Vérifier que la ligne est valide (nom non vide, >= 2 caractères, niveau et catégorie sélectionnés)
        if (!empty($nom) && strlen($nom) >= 2 && !empty($niveau) && !empty($categorie) && $heures >= 0) {
            $competence = new Competence($nom, $niveau, $categorie, $heures, $id_user);  // Créer l'objet
            $competenceC->ajouterCompetence($competence);  // Ajouter dans la base
            $countAjoutes++;                       // Incrémenter le compteur
        }
    }
    
    // Message de succès ou d'erreur selon le nombre ajouté
    if ($countAjoutes > 0) {
        $_SESSION['success'] = $countAjoutes . ' compétence(s) ajoutée(s) !';
    } else {
        $_SESSION['erreur'] = 'Aucune compétence valide à ajouter';
    }
    
    echo '<script>window.location.href="frontdessign.php?page=competences";</script>';  // Rediriger
    exit();
}

// --- MODIFICATION EN LIGNE ---
// Déclenché par : clic sur ✏️ puis ✅ dans le tableau
if (isset($_POST['action']) && $_POST['action'] == 'modifier_ligne') {
    $id = $_POST['id_competence'];                 // ID de la compétence à modifier
    $nom = trim($_POST['nom_competence']);         // Nouveau nom
    $niveau = $_POST['niveau'];                    // Nouveau niveau
    $categorie = $_POST['categorie'];              // Nouvelle catégorie
    $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;  // Nouvelles heures
    
    // VALIDATION DES CHAMPS
    $erreur = '';
    if (empty($nom)) $erreur = 'Le nom est obligatoire';
    elseif (strlen($nom) < 2) $erreur = 'Minimum 2 caractères';
    elseif (empty($niveau)) $erreur = 'Le niveau est obligatoire';
    elseif (empty($categorie)) $erreur = 'La catégorie est obligatoire';
    elseif ($heures < 0) $erreur = 'Les heures doivent être positives';
    
    if ($erreur == '') {                           // Si pas d'erreur
        $competence = new Competence($nom, $niveau, $categorie, $heures);  // Créer l'objet
        $competenceC->modifierCompetence($competence, $id);  // Modifier dans la base
        $_SESSION['success'] = 'Compétence modifiée !';
    } else {
        $_SESSION['erreur'] = $erreur;             // Stocker l'erreur
    }
    echo '<script>window.location.href="frontdessign.php?page=competences";</script>';  // Rediriger
    exit();
}

// ============================================================
// RÉCUPÉRATION DES DONNÉES POUR L'AFFICHAGE
// ============================================================
$competences = $competenceC->getCompetencesByUser($_SESSION['user_id']);  // Liste des compétences
$totalHeures = $competenceC->getTotalHeuresByUser($_SESSION['user_id']);  // Total des heures
$nbCompetences = $competenceC->countCompetencesByUser($_SESSION['user_id']);  // Nombre de compétences

$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';  // ID en cours d'édition
?>

<!-- ============================================================
     CSS SPÉCIFIQUE À LA PAGE COMPÉTENCES (préfixe -cf)
     ============================================================ -->
<style>
    /* Cartes statistiques (Total compétences, Heures échangées) */
    .stats-cards-cf { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card-cf { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-align: center; }
    .stat-card-cf h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
    .stat-card-cf .number { color: #2575fc; font-size: 32px; font-weight: bold; }
    
    /* Messages d'erreur et de succès */
    .error-message-cf { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
    .success-message-cf { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
    
    /* Formulaire d'ajout */
    .add-form-cf { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    
    /* Ligne de formulaire (une par compétence) */
    .competence-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 10px; flex-wrap: wrap; }
    .form-group-cf { flex: 1; min-width: 150px; }
    .form-group-cf label { display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px; }
    .form-group-cf input, .form-group-cf select { width: 100%; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
    .form-group-cf input:focus, .form-group-cf select:focus { outline: none; border-color: #2575fc; }
    
    /* Styles pour la validation - bordures vertes/rouges */
    .form-group-cf input.valid, .form-group-cf select.valid { border-color: #10b981 !important; }
    .form-group-cf input.invalid, .form-group-cf select.invalid { border-color: #ef4444 !important; }
    
    /* Messages sous les champs */
    .validation-message-cf { font-size: 11px; margin-top: 4px; min-height: 16px; }
    .validation-message-cf.valid { color: #10b981; }
    .validation-message-cf.invalid { color: #ef4444; }
    
    /* Boutons + et - pour ajouter/supprimer des lignes */
    .btn-row { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 18px; height: 38px; width: 38px; display: flex; align-items: center; justify-content: center; }
    .btn-add-row { background: #10b981; color: white; }        /* Bouton + (vert) */
    .btn-add-row:hover { background: #059669; }
    .btn-remove-row { background: #ef4444; color: white; }     /* Bouton - (rouge) */
    .btn-remove-row:hover { background: #dc2626; }
    
    /* Bouton Enregistrer tout - grisé si invalide */
    .btn-save-all { padding: 12px 30px; background: #2575fc; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 15px; opacity: 0.5; transition: 0.3s; }
    .btn-save-all.enabled { opacity: 1; }           /* Bouton actif */
    .btn-save-all.enabled:hover { background: #6a11cb; }
    
    /* Filtres (recherche + niveau) */
    .filters-cf { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .filters-cf input, .filters-cf select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
    .filters-cf button { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
    .btn-filter-cf { background: #2575fc; color: white; }
    .btn-filter-cf:hover { background: #6a11cb; }
    .btn-reset-cf { background: #e2e8f0; color: #475569; }
    
    /* Tableau des compétences */
    .table-container-cf { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .table-container-cf table { width: 100%; border-collapse: collapse; }
    .table-container-cf th { background: #f1f5f9; color: #64748b; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 600; }
    .table-container-cf td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
    .table-container-cf tr:hover { background: #fafafa; }
    .table-container-cf td input, .table-container-cf td select { width: 100%; padding: 6px 8px; border: 1.5px solid #cbd5e1; border-radius: 5px; font-size: 13px; background: #fff; }
    .table-container-cf td input:focus, .table-container-cf td select:focus { border-color: #2575fc; outline: none; }
    
    /* Badges de niveau (couleurs différentes selon le niveau) */
    .badge-niveau-cf { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .badge-debutant-cf { background: #bbf7d0; color: #166534; }       /* Vert */
    .badge-intermediaire-cf { background: #fef3c7; color: #92400e; }  /* Jaune */
    .badge-expert-cf { background: #fecaca; color: #991b1b; }         /* Rouge */
    
    /* Boutons d'action (modifier, supprimer, enregistrer, annuler) */
    .btn-cf { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 12px; font-weight: 600; margin: 0 2px; transition: 0.2s; }
    .btn-edit-cf { background: #93c5fd; color: #1e40af; }      /* ✏️ Modifier */
    .btn-delete-cf { background: #fca5a5; color: #991b1b; }    /* 🗑️ Supprimer */
    .btn-save-cf { background: #86efac; color: #166534; }      /* ✅ Enregistrer */
    .btn-cancel-cf { background: #e2e8f0; color: #475569; }    /* ❌ Annuler */
    .btn-cf:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    
    .empty-cf { text-align: center; padding: 50px; color: #94a3b8; font-style: italic; }
    .total-heures-cf { margin-top: 20px; text-align: right; font-weight: 600; color: #4c1d95; }
</style>

<!-- ============================================================
     CARTES STATISTIQUES
     ============================================================ -->
<div class="stats-cards-cf">
    <div class="stat-card-cf">
        <h3>Total compétences</h3>
        <div class="number"><?php echo $nbCompetences; ?></div>
    </div>
    <div class="stat-card-cf">
        <h3>Heures échangées</h3>
        <div class="number"><?php echo $totalHeures; ?>h</div>
    </div>
</div>

<!-- ============================================================
     MESSAGES D'ERREUR ET DE SUCCÈS
     ============================================================ -->
<?php if(isset($_SESSION['erreur'])): ?>
    <div class="error-message-cf"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['success'])): ?>
    <div class="success-message-cf"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<!-- ============================================================
     FORMULAIRE D'AJOUT AVEC CONTRÔLE DE SAISIE
     ============================================================ -->
<div class="add-form-cf">
    <form method="POST" action="" id="addMultipleForm" onsubmit="return validerFormulaireCF()">
        <input type="hidden" name="action" value="ajouter_multiple">
        
        <div id="competencesRows">
            <!-- Ligne 1 - chaque champ a une classe et un message de validation -->
            <div class="competence-row">
                <div class="form-group-cf">
                    <label>Nom *</label>
                    <input type="text" name="nom_competence[]" class="nom-input-cf" placeholder="Ex: Développement Web" oninput="validerToutesLesLignesCF()">
                    <div class="validation-message-cf"></div>
                </div>
                <div class="form-group-cf">
                    <label>Niveau *</label>
                    <select name="niveau[]" class="niveau-select-cf" onchange="validerToutesLesLignesCF()">
                        <option value="">Sélectionner</option>
                        <option value="Debutant">Débutant</option>
                        <option value="Intermediaire">Intermédiaire</option>
                        <option value="Expert">Expert</option>
                    </select>
                    <div class="validation-message-cf"></div>
                </div>
                <div class="form-group-cf">
                    <label>Catégorie *</label>
                    <select name="categorie[]" class="categorie-select-cf" onchange="validerToutesLesLignesCF()">
                        <option value="">Sélectionner</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Design">Design</option>
                        <option value="Langues">Langues</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Audiovisuel">Audiovisuel</option>
                        <option value="Art">Art</option>
                        <option value="Management">Management</option>
                        <option value="Autre">Autre</option>
                    </select>
                    <div class="validation-message-cf"></div>
                </div>
                <div class="form-group-cf">
                    <label>Heures</label>
                    <input type="number" name="heures_echangees[]" class="heures-input-cf" min="0" value="0" oninput="validerToutesLesLignesCF()">
                    <div class="validation-message-cf"></div>
                </div>
                <button type="button" class="btn-row btn-add-row" onclick="ajouterLigneCF()" title="Ajouter une ligne">+</button>
            </div>
        </div>
        
        <button type="submit" class="btn-save-all" id="btnEnregistrerCF" disabled>💾 Enregistrer tout</button>
    </form>
</div>

<!-- ============================================================
     FILTRES (Recherche + Niveau)
     ============================================================ -->
<div class="filters-cf">
    <input type="text" placeholder="🔍 Rechercher..." id="searchInput" style="flex:1;">
    <select id="filterNiveau">
        <option value="">Tous les niveaux</option>
        <option value="Debutant">Débutant</option>
        <option value="Intermediaire">Intermédiaire</option>
        <option value="Expert">Expert</option>
    </select>
    <button onclick="filtrerCompetences()" class="btn-filter-cf">Filtrer</button>
    <button onclick="reinitialiserFiltres()" class="btn-reset-cf">Réinitialiser</button>
</div>

<!-- ============================================================
     TABLEAU DES COMPÉTENCES
     ============================================================ -->
<div class="table-container-cf">
    <table id="competencesTable">
        <thead>
            <tr>
                <th>Compétence</th>
                <th>Niveau</th>
                <th>Catégorie</th>
                <th>Heures</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php if ($competences && count($competences) > 0): ?>
                <?php foreach ($competences as $c): ?>
                    <?php if ($edit_id == $c['id_competence']): ?>
                        <!-- ============================================
                             MODE ÉDITION (quand on clique sur ✏️)
                             ============================================ -->
                        <tr style="background:#f0f4ff;" data-niveau="<?php echo $c['niveau']; ?>">
                            <form method="POST" action="" style="display:contents;">
                                <input type="hidden" name="action" value="modifier_ligne">
                                <input type="hidden" name="id_competence" value="<?php echo $c['id_competence']; ?>">
                                <td><input type="text" name="nom_competence" value="<?php echo $c['nom_competence']; ?>"></td>
                                <td>
                                    <select name="niveau">
                                        <option value="Debutant" <?php if($c['niveau']=='Debutant') echo 'selected'; ?>>Débutant</option>
                                        <option value="Intermediaire" <?php if($c['niveau']=='Intermediaire') echo 'selected'; ?>>Intermédiaire</option>
                                        <option value="Expert" <?php if($c['niveau']=='Expert') echo 'selected'; ?>>Expert</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="categorie">
                                        <option value="Informatique" <?php if($c['categorie']=='Informatique') echo 'selected'; ?>>Informatique</option>
                                        <option value="Design" <?php if($c['categorie']=='Design') echo 'selected'; ?>>Design</option>
                                        <option value="Langues" <?php if($c['categorie']=='Langues') echo 'selected'; ?>>Langues</option>
                                        <option value="Marketing" <?php if($c['categorie']=='Marketing') echo 'selected'; ?>>Marketing</option>
                                        <option value="Audiovisuel" <?php if($c['categorie']=='Audiovisuel') echo 'selected'; ?>>Audiovisuel</option>
                                        <option value="Art" <?php if($c['categorie']=='Art') echo 'selected'; ?>>Art</option>
                                        <option value="Management" <?php if($c['categorie']=='Management') echo 'selected'; ?>>Management</option>
                                        <option value="Autre" <?php if($c['categorie']=='Autre') echo 'selected'; ?>>Autre</option>
                                    </select>
                                </td>
                                <td><input type="number" name="heures_echangees" min="0" value="<?php echo $c['heures_echangees']; ?>" style="width:80px;"></td>
                                <td>
                                    <button type="submit" class="btn-cf btn-save-cf">✅</button>
                                    <a href="frontdessign.php?page=competences" class="btn-cf btn-cancel-cf">❌</a>
                                </td>
                            </form>
                        </tr>
                    <?php else: ?>
                        <!-- ============================================
                             MODE AFFICHAGE NORMAL
                             ============================================ -->
                        <tr data-niveau="<?php echo $c['niveau']; ?>">
                            <td><?php echo $c['nom_competence']; ?></td>
                            <td>
                                <?php 
                                // Déterminer la couleur du badge selon le niveau
                                $niveauClass = '';
                                if ($c['niveau'] == 'Debutant') $niveauClass = 'badge-debutant-cf';
                                elseif ($c['niveau'] == 'Intermediaire') $niveauClass = 'badge-intermediaire-cf';
                                elseif ($c['niveau'] == 'Expert') $niveauClass = 'badge-expert-cf';
                                ?>
                                <span class="badge-niveau-cf <?php echo $niveauClass; ?>"><?php echo $c['niveau']; ?></span>
                            </td>
                            <td><?php echo $c['categorie']; ?></td>
                            <td><?php echo $c['heures_echangees']; ?>h</td>
                            <td>
                                <!-- Bouton Modifier : ajoute ?edit_id=X à l'URL -->
                                <a href="frontdessign.php?page=competences&edit_id=<?php echo $c['id_competence']; ?>" class="btn-cf btn-edit-cf">✏️</a>
                                <!-- Bouton Supprimer : ajoute ?action=delete&id=X à l'URL -->
                                <a href="frontdessign.php?page=competences&action=delete&id=<?php echo $c['id_competence']; ?>" class="btn-cf btn-delete-cf" onclick="return confirm('Supprimer cette compétence ?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="empty-cf">Aucune compétence ajoutée</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Total des heures -->
<div class="total-heures-cf">Total heures échangées : <?php echo $totalHeures; ?>h</div>

<!-- ============================================================
     JAVASCRIPT - Gestion des lignes dynamiques, filtres et validation
     ============================================================ -->
<script>
// ============================================================
// VÉRIFIE TOUS LES CHAMPS DE TOUTES LES LIGNES
// Active le bouton seulement si tout est valide
// ============================================================
function validerToutesLesLignesCF() {
    var toutesValides = true;  // On suppose que tout est bon au départ
    
    // Parcourir chaque ligne du formulaire
    document.querySelectorAll('#competencesRows .competence-row').forEach(function(row) {
        // Récupérer les 4 champs de cette ligne
        var nomInput = row.querySelector('.nom-input-cf');
        var niveauSelect = row.querySelector('.niveau-select-cf');
        var categorieSelect = row.querySelector('.categorie-select-cf');
        var heuresInput = row.querySelector('.heures-input-cf');
        
        // Récupérer les 4 messages de validation de cette ligne
        var messages = row.querySelectorAll('.validation-message-cf');
        var nomMsg = messages[0];       // Message pour le nom
        var niveauMsg = messages[1];    // Message pour le niveau
        var categorieMsg = messages[2]; // Message pour la catégorie
        var heuresMsg = messages[3];    // Message pour les heures
        
        var ligneValide = true;  // Cette ligne est valide par défaut
        
        // --- Vérifier le nom ---
        var nom = nomInput.value.trim();
        if (nom === '') {
            nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
            nomMsg.textContent = '❌ Obligatoire';
            nomMsg.classList.add('invalid'); nomMsg.classList.remove('valid');
            ligneValide = false;
        } else if (nom.length < 2) {
            nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
            nomMsg.textContent = '❌ Minimum 2 caractères';
            nomMsg.classList.add('invalid'); nomMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            nomInput.classList.add('valid'); nomInput.classList.remove('invalid');
            nomMsg.textContent = '✅ Valide';
            nomMsg.classList.add('valid'); nomMsg.classList.remove('invalid');
        }
        
        // --- Vérifier le niveau ---
        if (niveauSelect.value === '') {
            niveauSelect.classList.add('invalid'); niveauSelect.classList.remove('valid');
            niveauMsg.textContent = '❌ Obligatoire';
            niveauMsg.classList.add('invalid'); niveauMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            niveauSelect.classList.add('valid'); niveauSelect.classList.remove('invalid');
            niveauMsg.textContent = '✅ Valide';
            niveauMsg.classList.add('valid'); niveauMsg.classList.remove('invalid');
        }
        
        // --- Vérifier la catégorie ---
        if (categorieSelect.value === '') {
            categorieSelect.classList.add('invalid'); categorieSelect.classList.remove('valid');
            categorieMsg.textContent = '❌ Obligatoire';
            categorieMsg.classList.add('invalid'); categorieMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            categorieSelect.classList.add('valid'); categorieSelect.classList.remove('invalid');
            categorieMsg.textContent = '✅ Valide';
            categorieMsg.classList.add('valid'); categorieMsg.classList.remove('invalid');
        }
        
        // --- Vérifier les heures (doivent être ≥ 0) ---
        var heures = parseInt(heuresInput.value) || 0;
        if (heures < 0) {
            heuresInput.classList.add('invalid'); heuresInput.classList.remove('valid');
            heuresMsg.textContent = '❌ Doit être ≥ 0';
            heuresMsg.classList.add('invalid'); heuresMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            heuresInput.classList.add('valid'); heuresInput.classList.remove('invalid');
            heuresMsg.textContent = '✅ Valide';
            heuresMsg.classList.add('valid'); heuresMsg.classList.remove('invalid');
        }
        
        // Si cette ligne a une erreur → tout le formulaire est invalide
        if (!ligneValide) toutesValides = false;
    });
    
    // Activer ou désactiver le bouton "Enregistrer tout"
    var btn = document.getElementById('btnEnregistrerCF');
    if (toutesValides) {
        btn.disabled = false;
        btn.classList.add('enabled');
    } else {
        btn.disabled = true;
        btn.classList.remove('enabled');
    }
}

// ============================================================
// AJOUTER UNE NOUVELLE LIGNE (avec validation)
// ============================================================
function ajouterLigneCF() {
    var container = document.getElementById('competencesRows');
    var newRow = document.createElement('div');
    newRow.className = 'competence-row';
    newRow.innerHTML = `
        <div class="form-group-cf"><label>Nom *</label><input type="text" name="nom_competence[]" class="nom-input-cf" placeholder="Ex: Développement Web" oninput="validerToutesLesLignesCF()"><div class="validation-message-cf"></div></div>
        <div class="form-group-cf"><label>Niveau *</label><select name="niveau[]" class="niveau-select-cf" onchange="validerToutesLesLignesCF()"><option value="">Sélectionner</option><option value="Debutant">Débutant</option><option value="Intermediaire">Intermédiaire</option><option value="Expert">Expert</option></select><div class="validation-message-cf"></div></div>
        <div class="form-group-cf"><label>Catégorie *</label><select name="categorie[]" class="categorie-select-cf" onchange="validerToutesLesLignesCF()"><option value="">Sélectionner</option><option value="Informatique">Informatique</option><option value="Design">Design</option><option value="Langues">Langues</option><option value="Marketing">Marketing</option><option value="Audiovisuel">Audiovisuel</option><option value="Art">Art</option><option value="Management">Management</option><option value="Autre">Autre</option></select><div class="validation-message-cf"></div></div>
        <div class="form-group-cf"><label>Heures</label><input type="number" name="heures_echangees[]" class="heures-input-cf" min="0" value="0" oninput="validerToutesLesLignesCF()"><div class="validation-message-cf"></div></div>
        <button type="button" class="btn-row btn-add-row" onclick="ajouterLigneCF()" title="Ajouter une ligne">+</button>
        <button type="button" class="btn-row btn-remove-row" onclick="supprimerLigneCF(this)" title="Supprimer cette ligne">-</button>
    `;
    container.appendChild(newRow);
    validerToutesLesLignesCF();  // Revalider après avoir ajouté la ligne
}

// ============================================================
// SUPPRIMER UNE LIGNE
// ============================================================
function supprimerLigneCF(btn) {
    var row = btn.parentElement;
    var container = document.getElementById('competencesRows');
    if (container.children.length > 1) {
        row.remove();
        validerToutesLesLignesCF();  // Revalider après suppression
    }
}

// ============================================================
// VÉRIFICATION FINALE AVANT ENVOI DU FORMULAIRE
// ============================================================
function validerFormulaireCF() {
    validerToutesLesLignesCF();
    return !document.getElementById('btnEnregistrerCF').disabled;
}

// ============================================================
// FILTRES
// ============================================================
function filtrerCompetences() {
    var sv = document.getElementById('searchInput').value.toLowerCase();
    var nv = document.getElementById('filterNiveau').value;
    document.querySelectorAll('#tableBody tr').forEach(function(r) {
        if (r.cells.length < 5) return;
        var n = r.cells[0].textContent.toLowerCase();
        var c = r.cells[2].textContent.toLowerCase();
        var match = (n.includes(sv) || c.includes(sv)) && (nv === '' || r.getAttribute('data-niveau') === nv);
        r.style.display = match ? '' : 'none';
    });
}
function reinitialiserFiltres() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterNiveau').value = '';
    document.querySelectorAll('#tableBody tr').forEach(function(r) { r.style.display = ''; });
}
document.getElementById('searchInput').addEventListener('keyup', filtrerCompetences);
</script>