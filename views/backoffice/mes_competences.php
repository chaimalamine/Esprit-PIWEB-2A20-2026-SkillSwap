<?php 
// ============================================================
// DÉMARRAGE DE LA SESSION + VÉRIFICATION CONNEXION
// ============================================================
session_start();

// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');  // Rediriger vers connexion si pas connecté
    exit();
}

// ============================================================
// INCLUSION DU CONTRÔLEUR CompetenceC
// CompetenceC = gère toutes les opérations sur les compétences
// ============================================================
include '../../controllers/CompetenceC.php';

// créer un objet pour accéder aux fonctions
$competenceC = new CompetenceC();

// ============================================================
// SUPPRESSION D'UNE COMPÉTENCE
// Déclenché par le bouton 🗑️ dans le tableau
// ============================================================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];                          // Récupérer l'ID de la compétence à supprimer
    $competenceC->supprimerCompetence($id);     // Appeler la fonction de suppression
    header('Location: mes_competences.php');    // Recharger la page
    exit();
}

// ============================================================
// AJOUT MULTIPLE DE COMPÉTENCES (VALIDATION PHP CÔTÉ SERVEUR)
// Déclenché par le bouton "Enregistrer tout"
// ============================================================
if (isset($_POST['action']) && $_POST['action'] == 'ajouter_multiple') {
    
    // Récupérer les tableaux de données (une entrée par ligne du formulaire)
    $noms = $_POST['nom_competence'];            // Tableau des noms
    $niveaux = $_POST['niveau'];                 // Tableau des niveaux
    $categories = $_POST['categorie'];           // Tableau des catégories
    $heuresArr = $_POST['heures_echangees'];     // Tableau des heures
    $id_user = $_SESSION['user_id'];             // ID de l'utilisateur connecté
    
    $countAjoutes = 0;                           // Compteur : combien de compétences ajoutées
    
    // Boucler sur chaque ligne du formulaire
    for ($i = 0; $i < count($noms); $i++) {
        $nom = trim($noms[$i]);                  // Nettoyer le nom (enlever espaces)
        $niveau = $niveaux[$i];                  // Niveau de cette ligne
        $categorie = $categories[$i];            // Catégorie de cette ligne
        $heures = isset($heuresArr[$i]) ? $heuresArr[$i] : 0;  // Heures (0 si vide)
        
        // Vérifier que la ligne est valide avant d'ajouter
        // Conditions : nom non vide, ≥ 2 caractères, niveau choisi, catégorie choisie, heures ≥ 0
        if (!empty($nom) && strlen($nom) >= 2 && !empty($niveau) && !empty($categorie) && $heures >= 0) {
            // Créer un objet Competence avec les valeurs de cette ligne
            $competence = new Competence($nom, $niveau, $categorie, $heures, $id_user);
            // Appeler la fonction d'ajout (définie dans CompetenceC.php)
            $competenceC->ajouterCompetence($competence);
            $countAjoutes++;                     // Incrémenter le compteur
        }
    }
    
    // Définir le message selon le résultat
    if ($countAjoutes > 0) {
        $_SESSION['success'] = $countAjoutes . ' compétence(s) ajoutée(s) !';
    } else {
        $_SESSION['erreur'] = 'Aucune compétence valide à ajouter';
    }
    
    // Rediriger vers la liste
    header('Location: mes_competences.php');
    exit();
}

// ============================================================
// MODIFICATION EN LIGNE D'UNE COMPÉTENCE (VALIDATION PHP)
// ============================================================
if (isset($_POST['action']) && $_POST['action'] == 'modifier_ligne') {
    
    $id = $_POST['id_competence'];               // ID de la compétence à modifier
    $nom = trim($_POST['nom_competence']);       // Nouveau nom
    $niveau = $_POST['niveau'];                  // Nouveau niveau
    $categorie = $_POST['categorie'];            // Nouvelle catégorie
    $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;  // Nouvelles heures
    
    // VALIDATION des champs
    $erreur = '';
    if (empty($nom)) $erreur = 'Le nom est obligatoire';
    elseif (strlen($nom) < 2) $erreur = 'Minimum 2 caractères';
    elseif (empty($niveau)) $erreur = 'Le niveau est obligatoire';
    elseif (empty($categorie)) $erreur = 'La catégorie est obligatoire';
    elseif ($heures < 0) $erreur = 'Les heures doivent être positives';
    
    if ($erreur == '') {
        $competence = new Competence($nom, $niveau, $categorie, $heures);
        $competenceC->modifierCompetence($competence, $id);
        $_SESSION['success'] = 'Compétence modifiée !';
    } else {
        $_SESSION['erreur'] = $erreur;
    }
    
    header('Location: mes_competences.php');
    exit();
}

// ============================================================
// RÉCUPÉRER LES DONNÉES POUR L'AFFICHAGE
// ============================================================
$competences = $competenceC->getCompetencesByUser($_SESSION['user_id']);
$totalHeures = $competenceC->getTotalHeuresByUser($_SESSION['user_id']);
$nbCompetences = $competenceC->countCompetencesByUser($_SESSION['user_id']);
$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Compétences - SkillSwap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f7; display: flex; }
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .sidebar a.active { background: rgba(255,255,255,0.2); }
        .main { margin-left: 220px; padding: 20px; flex: 1; }
        .header { background: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .header h2 { color: #4c1d95; font-size: 24px; }
        .stats-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-align: center; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card .number { color: #2575fc; font-size: 32px; font-weight: bold; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
        .success-message { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
        .add-form { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .competence-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 10px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
        .form-group label { display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #2575fc; }
        /* Styles pour la validation en temps réel */
        .form-group input.valid, .form-group select.valid { border-color: #10b981 !important; }
        .form-group input.invalid, .form-group select.invalid { border-color: #ef4444 !important; }
        .validation-message { font-size: 11px; margin-top: 4px; min-height: 16px; }
        .validation-message.valid { color: #10b981; }
        .validation-message.invalid { color: #ef4444; }
        .btn-row { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 18px; height: 38px; width: 38px; display: flex; align-items: center; justify-content: center; }
        .btn-add-row { background: #10b981; color: white; }
        .btn-add-row:hover { background: #059669; }
        .btn-remove-row { background: #ef4444; color: white; }
        .btn-remove-row:hover { background: #dc2626; }
        .btn-save-all { padding: 12px 30px; background: #2575fc; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 15px; opacity: 0.5; transition: 0.3s; }
        .btn-save-all.enabled { opacity: 1; }
        .btn-save-all.enabled:hover { background: #6a11cb; }
        .filters { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .filters input, .filters select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
        .filters button { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .btn-filter { background: #2575fc; color: white; }
        .btn-filter:hover { background: #6a11cb; }
        .btn-reset { background: #e2e8f0; color: #475569; }
        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #64748b; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
        tr:hover { background: #fafafa; }
        td input, td select { width: 100%; padding: 6px 8px; border: 1.5px solid #cbd5e1; border-radius: 5px; font-size: 13px; background: #fff; }
        td input:focus, td select:focus { border-color: #2575fc; outline: none; }
        .badge-niveau { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-debutant { background: #bbf7d0; color: #166534; }
        .badge-intermediaire { background: #fef3c7; color: #92400e; }
        .badge-expert { background: #fecaca; color: #991b1b; }
        .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 12px; font-weight: 600; margin: 0 2px; transition: 0.2s; }
        .btn-edit { background: #93c5fd; color: #1e40af; }
        .btn-delete { background: #fca5a5; color: #991b1b; }
        .btn-save { background: #86efac; color: #166534; }
        .btn-cancel { background: #e2e8f0; color: #475569; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .empty { text-align: center; padding: 50px; color: #94a3b8; font-style: italic; }
        .total-heures { margin-top: 20px; text-align: right; font-weight: 600; color: #4c1d95; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="design.php">Dashboard</a>
    <a href="liste_users.php">Liste des utilisateurs</a>
    <a href="mes_competences.php" class="active">Mes Compétences</a>
    <a href="#">Offres</a>
    <a href="#">Messages</a>
    <a href="profil.php">Profil</a>
    <a href="../frontoffice/frontdessign.php" style="margin-top: 30px; background: rgba(255,255,255,0.1);">← Retour au site</a>
</div>

<div class="main">
    <div class="header"><h2>Mes Compétences</h2></div>

    <div class="stats-cards">
        <div class="stat-card"><h3>Total compétences</h3><div class="number"><?php echo $nbCompetences; ?></div></div>
        <div class="stat-card"><h3>Heures échangées</h3><div class="number"><?php echo $totalHeures; ?>h</div></div>
    </div>

    <?php if(isset($_SESSION['erreur'])): ?><div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div><?php endif; ?>
    <?php if(isset($_SESSION['success'])): ?><div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div><?php endif; ?>

    <!-- ============================================================
         FORMULAIRE D'AJOUT MULTIPLE AVEC CONTRÔLE DE SAISIE
         ============================================================ -->
    <div class="add-form">
        <form method="POST" action="" id="addMultipleForm" onsubmit="return validerFormulaire()">
            <input type="hidden" name="action" value="ajouter_multiple">
            <div id="competencesRows">
                <!-- Ligne 1 (avec validation en temps réel) -->
                <div class="competence-row" data-row="0">
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom_competence[]" class="nom-input" placeholder="Ex: Développement Web" oninput="validerToutesLesLignes()">
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label>Niveau *</label>
                        <select name="niveau[]" class="niveau-select" onchange="validerToutesLesLignes()">
                            <option value="">Sélectionner</option>
                            <option value="Debutant">Débutant</option>
                            <option value="Intermediaire">Intermédiaire</option>
                            <option value="Expert">Expert</option>
                        </select>
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="categorie[]" class="categorie-select" onchange="validerToutesLesLignes()">
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
                        <div class="validation-message"></div>
                    </div>
                    <div class="form-group">
                        <label>Heures</label>
                        <input type="number" name="heures_echangees[]" class="heures-input" min="0" value="0" oninput="validerToutesLesLignes()">
                        <div class="validation-message"></div>
                    </div>
                    <button type="button" class="btn-row btn-add-row" onclick="ajouterLigne()" title="Ajouter une ligne">+</button>
                </div>
            </div>
            <button type="submit" class="btn-save-all" id="btnEnregistrer" disabled>💾 Enregistrer tout</button>
        </form>
    </div>

    <!-- FILTRES -->
    <div class="filters">
        <input type="text" placeholder="🔍 Rechercher..." id="searchInput" style="flex:1;">
        <select id="filterNiveau"><option value="">Tous les niveaux</option><option value="Debutant">Débutant</option><option value="Intermediaire">Intermédiaire</option><option value="Expert">Expert</option></select>
        <button onclick="filtrerCompetences()" class="btn-filter">Filtrer</button>
        <button onclick="reinitialiserFiltres()" class="btn-reset">Réinitialiser</button>
    </div>

    <!-- TABLEAU -->
    <div class="table-container">
        <table id="competencesTable">
            <thead><tr><th>Compétence</th><th>Niveau</th><th>Catégorie</th><th>Heures</th><th>Actions</th></tr></thead>
            <tbody id="tableBody">
                <?php if ($competences && count($competences) > 0): ?>
                    <?php foreach ($competences as $c): ?>
                        <?php if ($edit_id == $c['id_competence']): ?>
                            <tr style="background:#f0f4ff;" data-niveau="<?php echo $c['niveau']; ?>">
                                <form method="POST" action="" style="display:contents;">
                                    <input type="hidden" name="action" value="modifier_ligne"><input type="hidden" name="id_competence" value="<?php echo $c['id_competence']; ?>">
                                    <td><input type="text" name="nom_competence" value="<?php echo $c['nom_competence']; ?>"></td>
                                    <td><select name="niveau"><option value="Debutant" <?php if($c['niveau']=='Debutant') echo 'selected'; ?>>Débutant</option><option value="Intermediaire" <?php if($c['niveau']=='Intermediaire') echo 'selected'; ?>>Intermédiaire</option><option value="Expert" <?php if($c['niveau']=='Expert') echo 'selected'; ?>>Expert</option></select></td>
                                    <td><select name="categorie"><option value="Informatique" <?php if($c['categorie']=='Informatique') echo 'selected'; ?>>Informatique</option><option value="Design" <?php if($c['categorie']=='Design') echo 'selected'; ?>>Design</option><option value="Langues" <?php if($c['categorie']=='Langues') echo 'selected'; ?>>Langues</option><option value="Marketing" <?php if($c['categorie']=='Marketing') echo 'selected'; ?>>Marketing</option><option value="Audiovisuel" <?php if($c['categorie']=='Audiovisuel') echo 'selected'; ?>>Audiovisuel</option><option value="Art" <?php if($c['categorie']=='Art') echo 'selected'; ?>>Art</option><option value="Management" <?php if($c['categorie']=='Management') echo 'selected'; ?>>Management</option><option value="Autre" <?php if($c['categorie']=='Autre') echo 'selected'; ?>>Autre</option></select></td>
                                    <td><input type="number" name="heures_echangees" min="0" value="<?php echo $c['heures_echangees']; ?>" style="width:80px;"></td>
                                    <td><button type="submit" class="btn btn-save">✅</button><a href="mes_competences.php" class="btn btn-cancel">❌</a></td>
                                </form>
                            </tr>
                        <?php else: ?>
                            <tr data-niveau="<?php echo $c['niveau']; ?>">
                                <td><?php echo $c['nom_competence']; ?></td>
                                <td><span class="badge-niveau <?php if($c['niveau']=='Debutant') echo 'badge-debutant'; elseif($c['niveau']=='Intermediaire') echo 'badge-intermediaire'; elseif($c['niveau']=='Expert') echo 'badge-expert'; ?>"><?php echo $c['niveau']; ?></span></td>
                                <td><?php echo $c['categorie']; ?></td>
                                <td><?php echo $c['heures_echangees']; ?>h</td>
                                <td><a href="?edit_id=<?php echo $c['id_competence']; ?>" class="btn btn-edit">✏️</a><a href="?action=delete&id=<?php echo $c['id_competence']; ?>" class="btn btn-delete" onclick="return confirm('Supprimer cette compétence ?')">🗑️</a></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty">Aucune compétence ajoutée</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="total-heures">Total heures échangées : <?php echo $totalHeures; ?>h</div>
</div>

<script>
// ============================================================
// CONTRÔLE DE SAISIE EN TEMPS RÉEL
// Valide toutes les lignes du formulaire à chaque modification
// ============================================================
function validerToutesLesLignes() {
    var toutesValides = true;  // Supposer que tout est valide au départ
    
    // Parcourir chaque ligne du formulaire
    document.querySelectorAll('#competencesRows .competence-row').forEach(function(row) {
        // Récupérer les champs de cette ligne
        var nomInput = row.querySelector('.nom-input');
        var niveauSelect = row.querySelector('.niveau-select');
        var categorieSelect = row.querySelector('.categorie-select');
        var heuresInput = row.querySelector('.heures-input');
        
        // Récupérer les messages de validation de cette ligne
        var messages = row.querySelectorAll('.validation-message');
        var nomMsg = messages[0];    // Message pour le nom
        var niveauMsg = messages[1]; // Message pour le niveau
        var categorieMsg = messages[2]; // Message pour la catégorie
        var heuresMsg = messages[3]; // Message pour les heures
        
        var ligneValide = true;  // Supposer que cette ligne est valide
        
        // --- VALIDATION DU NOM ---
        var nom = nomInput.value.trim();
        if (nom === '') {
            // Champ vide → erreur
            nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
            nomMsg.textContent = '❌ Obligatoire';
            nomMsg.classList.add('invalid'); nomMsg.classList.remove('valid');
            ligneValide = false;
        } else if (nom.length < 2) {
            // Trop court → erreur
            nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
            nomMsg.textContent = '❌ Minimum 2 caractères';
            nomMsg.classList.add('invalid'); nomMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            // Valide → succès
            nomInput.classList.add('valid'); nomInput.classList.remove('invalid');
            nomMsg.textContent = '✅ Valide';
            nomMsg.classList.add('valid'); nomMsg.classList.remove('invalid');
        }
        
        // --- VALIDATION DU NIVEAU ---
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
        
        // --- VALIDATION DE LA CATÉGORIE ---
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
        
        // --- VALIDATION DES HEURES ---
        var heures = parseInt(heuresInput.value) || 0;
        if (heures < 0) {
            heuresInput.classList.add('invalid'); heuresInput.classList.remove('valid');
            heuresMsg.textContent = '❌ Doit être ≥ 0';
            heuresMsg.classList.add('invalid'); heuresMsg.classList.remove('valid');
            ligneValide = false;
        } else {
            heuresInput.classList.add('valid'); heuresInput.classList.remove('invalid');
            heuresMsg.textContent = '✅ Valide';
            heuresMsg.classList.add('valid'); heuresMsg.classList.remove('valid');
        }
        
        // Si cette ligne n'est pas valide → tout le formulaire est invalide
        if (!ligneValide) {
            toutesValides = false;
        }
    });
    
    // Activer ou désactiver le bouton "Enregistrer tout"
    var btn = document.getElementById('btnEnregistrer');
    if (toutesValides) {
        btn.disabled = false;
        btn.classList.add('enabled');
    } else {
        btn.disabled = true;
        btn.classList.remove('enabled');
    }
}

// ============================================================
// AJOUTER UNE NOUVELLE LIGNE DE FORMULAIRE
// ============================================================
function ajouterLigne() {
    var container = document.getElementById('competencesRows');
    var newRow = document.createElement('div');
    newRow.className = 'competence-row';
    newRow.innerHTML = `
        <div class="form-group">
            <label>Nom *</label>
            <input type="text" name="nom_competence[]" class="nom-input" placeholder="Ex: Développement Web" oninput="validerToutesLesLignes()">
            <div class="validation-message"></div>
        </div>
        <div class="form-group">
            <label>Niveau *</label>
            <select name="niveau[]" class="niveau-select" onchange="validerToutesLesLignes()">
                <option value="">Sélectionner</option>
                <option value="Debutant">Débutant</option>
                <option value="Intermediaire">Intermédiaire</option>
                <option value="Expert">Expert</option>
            </select>
            <div class="validation-message"></div>
        </div>
        <div class="form-group">
            <label>Catégorie *</label>
            <select name="categorie[]" class="categorie-select" onchange="validerToutesLesLignes()">
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
            <div class="validation-message"></div>
        </div>
        <div class="form-group">
            <label>Heures</label>
            <input type="number" name="heures_echangees[]" class="heures-input" min="0" value="0" oninput="validerToutesLesLignes()">
            <div class="validation-message"></div>
        </div>
        <button type="button" class="btn-row btn-add-row" onclick="ajouterLigne()" title="Ajouter une ligne">+</button>
        <button type="button" class="btn-row btn-remove-row" onclick="supprimerLigne(this)" title="Supprimer cette ligne">-</button>
    `;
    container.appendChild(newRow);
    validerToutesLesLignes();  // Revalider après ajout
}

// ============================================================
// SUPPRIMER UNE LIGNE
// ============================================================
function supprimerLigne(btn) {
    var row = btn.parentElement;
    var container = document.getElementById('competencesRows');
    if (container.children.length > 1) {
        row.remove();
        validerToutesLesLignes();  // Revalider après suppression
    }
}

// ============================================================
// VALIDATION FINALE AVANT ENVOI
// ============================================================
function validerFormulaire() {
    validerToutesLesLignes();
    var btn = document.getElementById('btnEnregistrer');
    return !btn.disabled;  // Empêcher l'envoi si le bouton est désactivé
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
</body>
</html>