<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

include '../../controllers/CompetenceC.php';
$competenceC = new CompetenceC();

// --- SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $competenceC->supprimerCompetence($id);
    header('Location: mes_competences.php');
    exit();
}

// --- AJOUT RAPIDE ---
if (isset($_POST['action']) && $_POST['action'] == 'ajouter_rapide') {
    $nom = trim($_POST['nom_competence']);
    $niveau = $_POST['niveau'];
    $categorie = $_POST['categorie'];
    $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;
    $id_user = $_SESSION['user_id'];
    
    $erreur = '';
    if (empty($nom)) $erreur = 'Le nom est obligatoire';
    elseif (strlen($nom) < 2) $erreur = 'Minimum 2 caractères';
    elseif (empty($niveau)) $erreur = 'Le niveau est obligatoire';
    elseif (empty($categorie)) $erreur = 'La catégorie est obligatoire';
    elseif ($heures < 0) $erreur = 'Les heures doivent être positives';
    
    if ($erreur == '') {
        $competence = new Competence($nom, $niveau, $categorie, $heures, $id_user);
        $competenceC->ajouterCompetence($competence);
        $_SESSION['success'] = 'Compétence ajoutée !';
    } else {
        $_SESSION['erreur'] = $erreur;
    }
    header('Location: mes_competences.php');
    exit();
}

// --- MODIFICATION EN LIGNE ---
if (isset($_POST['action']) && $_POST['action'] == 'modifier_ligne') {
    $id = $_POST['id_competence'];
    $nom = trim($_POST['nom_competence']);
    $niveau = $_POST['niveau'];
    $categorie = $_POST['categorie'];
    $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;
    
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

// Récupérer les compétences de l'utilisateur connecté
$competences = $competenceC->getCompetencesByUser($_SESSION['user_id']);
$totalHeures = $competenceC->getTotalHeuresByUser($_SESSION['user_id']);
$nbCompetences = $competenceC->countCompetencesByUser($_SESSION['user_id']);

// ID en cours d'édition
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
        .header { background: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .header h2 { color: #4c1d95; font-size: 24px; }
        
        .stats-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-align: center; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card .number { color: #2575fc; font-size: 32px; font-weight: bold; }
        
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
        .success-message { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
        
        .add-form { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .add-form form { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
        .form-group label { display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #2575fc; }
        .form-group input.valid, .form-group select.valid { border-color: #10b981 !important; }
        .form-group input.invalid, .form-group select.invalid { border-color: #ef4444 !important; }
        
        .validation-message { font-size: 11px; margin-top: 4px; min-height: 16px; }
        .validation-message.valid { color: #10b981; }
        .validation-message.invalid { color: #ef4444; }
        
        .btn-add { padding: 8px 20px; background: #2575fc; border: none; color: white; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; height: 38px; opacity: 0.5; transition: 0.3s; }
        .btn-add.enabled { opacity: 1; }
        .btn-add.enabled:hover { background: #6a11cb; }
        
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
        <div class="header">
            <h2>Mes Compétences</h2>
        </div>

        <div class="stats-cards">
            <div class="stat-card">
                <h3>Total compétences</h3>
                <div class="number"><?php echo $nbCompetences; ?></div>
            </div>
            <div class="stat-card">
                <h3>Heures échangées</h3>
                <div class="number"><?php echo $totalHeures; ?>h</div>
            </div>
        </div>

        <?php if(isset($_SESSION['erreur'])): ?>
            <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- FORMULAIRE D'AJOUT RAPIDE -->
        <div class="add-form">
            <form method="POST" action="" id="addForm">
                <input type="hidden" name="action" value="ajouter_rapide">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom_competence" id="nom_competence" placeholder="Ex: Développement Web">
                    <div class="validation-message" id="nomMessage"></div>
                </div>
                <div class="form-group">
                    <label>Niveau *</label>
                    <select name="niveau" id="niveau">
                        <option value="">Sélectionner</option>
                        <option value="Debutant">Débutant</option>
                        <option value="Intermediaire">Intermédiaire</option>
                        <option value="Expert">Expert</option>
                    </select>
                    <div class="validation-message" id="niveauMessage"></div>
                </div>
                <div class="form-group">
                    <label>Catégorie *</label>
                    <select name="categorie" id="categorie">
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
                    <div class="validation-message" id="categorieMessage"></div>
                </div>
                <div class="form-group">
                    <label>Heures</label>
                    <input type="number" name="heures_echangees" id="heures_echangees" min="0" value="0">
                    <div class="validation-message" id="heuresMessage"></div>
                </div>
                <button type="submit" class="btn-add" id="submitBtn" disabled>+ Ajouter</button>
            </form>
        </div>

        <!-- FILTRES -->
        <div class="filters">
            <input type="text" placeholder="🔍 Rechercher..." id="searchInput" style="flex:1;">
            <select id="filterNiveau">
                <option value="">Tous les niveaux</option>
                <option value="Debutant">Débutant</option>
                <option value="Intermediaire">Intermédiaire</option>
                <option value="Expert">Expert</option>
            </select>
            <button onclick="filtrerCompetences()" class="btn-filter">Filtrer</button>
            <button onclick="reinitialiserFiltres()" class="btn-reset">Réinitialiser</button>
        </div>

        <!-- TABLEAU -->
        <div class="table-container">
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
                                            <button type="submit" class="btn btn-save">✅</button>
                                            <a href="mes_competences.php" class="btn btn-cancel">❌</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php else: ?>
                                <tr data-niveau="<?php echo $c['niveau']; ?>">
                                    <td><?php echo $c['nom_competence']; ?></td>
                                    <td>
                                        <?php 
                                        $niveauClass = '';
                                        if ($c['niveau'] == 'Debutant') $niveauClass = 'badge-debutant';
                                        elseif ($c['niveau'] == 'Intermediaire') $niveauClass = 'badge-intermediaire';
                                        elseif ($c['niveau'] == 'Expert') $niveauClass = 'badge-expert';
                                        ?>
                                        <span class="badge-niveau <?php echo $niveauClass; ?>"><?php echo $c['niveau']; ?></span>
                                    </td>
                                    <td><?php echo $c['categorie']; ?></td>
                                    <td><?php echo $c['heures_echangees']; ?>h</td>
                                    <td>
                                        <a href="?edit_id=<?php echo $c['id_competence']; ?>" class="btn btn-edit">✏️</a>
                                        <a href="?action=delete&id=<?php echo $c['id_competence']; ?>" class="btn btn-delete" onclick="return confirm('Supprimer cette compétence ?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="empty">Aucune compétence ajoutée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="total-heures">
            Total heures échangées : <?php echo $totalHeures; ?>h
        </div>
    </div>

    <script>
        // Validation en temps réel
        const nomInput = document.getElementById('nom_competence');
        const niveauSelect = document.getElementById('niveau');
        const categorieSelect = document.getElementById('categorie');
        const heuresInput = document.getElementById('heures_echangees');
        
        const nomMessage = document.getElementById('nomMessage');
        const niveauMessage = document.getElementById('niveauMessage');
        const categorieMessage = document.getElementById('categorieMessage');
        const heuresMessage = document.getElementById('heuresMessage');
        
        const submitBtn = document.getElementById('submitBtn');
        
        let nomValid = false;
        let niveauValid = false;
        let categorieValid = false;
        let heuresValid = true;
        
        function validateNom() {
            const nom = nomInput.value.trim();
            if (nom === '') {
                nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Obligatoire';
                nomMessage.classList.add('invalid'); nomMessage.classList.remove('valid');
                nomValid = false;
            } else if (nom.length < 2) {
                nomInput.classList.add('invalid'); nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Minimum 2 caractères';
                nomMessage.classList.add('invalid'); nomMessage.classList.remove('valid');
                nomValid = false;
            } else {
                nomInput.classList.add('valid'); nomInput.classList.remove('invalid');
                nomMessage.textContent = '✅ Valide';
                nomMessage.classList.add('valid'); nomMessage.classList.remove('invalid');
                nomValid = true;
            }
            updateButton();
        }
        
        function validateNiveau() {
            if (niveauSelect.value === '') {
                niveauSelect.classList.add('invalid'); niveauSelect.classList.remove('valid');
                niveauMessage.textContent = '❌ Obligatoire';
                niveauMessage.classList.add('invalid'); niveauMessage.classList.remove('valid');
                niveauValid = false;
            } else {
                niveauSelect.classList.add('valid'); niveauSelect.classList.remove('invalid');
                niveauMessage.textContent = '✅ Valide';
                niveauMessage.classList.add('valid'); niveauMessage.classList.remove('invalid');
                niveauValid = true;
            }
            updateButton();
        }
        
        function validateCategorie() {
            if (categorieSelect.value === '') {
                categorieSelect.classList.add('invalid'); categorieSelect.classList.remove('valid');
                categorieMessage.textContent = '❌ Obligatoire';
                categorieMessage.classList.add('invalid'); categorieMessage.classList.remove('valid');
                categorieValid = false;
            } else {
                categorieSelect.classList.add('valid'); categorieSelect.classList.remove('invalid');
                categorieMessage.textContent = '✅ Valide';
                categorieMessage.classList.add('valid'); categorieMessage.classList.remove('invalid');
                categorieValid = true;
            }
            updateButton();
        }
        
        function validateHeures() {
            const heures = heuresInput.value;
            if (heures < 0) {
                heuresInput.classList.add('invalid'); heuresInput.classList.remove('valid');
                heuresMessage.textContent = '❌ Positif requis';
                heuresMessage.classList.add('invalid'); heuresMessage.classList.remove('valid');
                heuresValid = false;
            } else {
                heuresInput.classList.add('valid'); heuresInput.classList.remove('invalid');
                heuresMessage.textContent = '✅ Valide';
                heuresMessage.classList.add('valid'); heuresMessage.classList.remove('invalid');
                heuresValid = true;
            }
            updateButton();
        }
        
        function updateButton() {
            if (nomValid && niveauValid && categorieValid && heuresValid) {
                submitBtn.disabled = false;
                submitBtn.classList.add('enabled');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('enabled');
            }
        }
        
        nomInput.addEventListener('input', validateNom);
        niveauSelect.addEventListener('change', validateNiveau);
        categorieSelect.addEventListener('change', validateCategorie);
        heuresInput.addEventListener('input', validateHeures);
        
        // Filtres
        function filtrerCompetences() {
            var searchValue = document.getElementById('searchInput').value.toLowerCase();
            var niveauValue = document.getElementById('filterNiveau').value;
            var rows = document.querySelectorAll('#tableBody tr');
            
            rows.forEach(function(row) {
                if (row.cells.length < 5) return;
                
                var nom = row.cells[0].textContent.toLowerCase();
                var categorie = row.cells[2].textContent.toLowerCase();
                var niveau = row.getAttribute('data-niveau');
                
                var matchSearch = (nom.includes(searchValue) || categorie.includes(searchValue));
                var matchNiveau = (niveauValue === '' || niveau === niveauValue);
                
                if (matchSearch && matchNiveau) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function reinitialiserFiltres() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterNiveau').value = '';
            var rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(function(row) {
                row.style.display = '';
            });
        }
        
        document.getElementById('searchInput').addEventListener('keyup', filtrerCompetences);
    </script>
</body>
</html>