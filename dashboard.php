<?php
require_once 'config.php';
require_once 'model/Event.php';
require_once 'model/Ressource.php';
require_once 'controller/EventController.php';
require_once 'controller/RessourceController.php';

session_start();
$eventController = new EventController();
$ressourceController = new RessourceController();

$errors = [];
$success = '';
$editMode = false;
$editEvent = null;

$search = $_GET['search'] ?? ''; // PARAMÈTRES DE RECHERCHE ET TRI (via GET)
$sort = $_GET['sort'] ?? '';

// (GET - afficher le formulaire)
if (isset($_GET['action']) && $_GET['action'] === 'edit_event' && isset($_GET['id'])) {
    $editEvent = $eventController->getEvent($_GET['id']);
    if ($editEvent) {
        $editMode = true;
    }
}

// GESTION DE L'AJOUT OU MODIFICATION (POST) 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_event'])) {
    
    // Récupération commune des données
    $titre          = trim($_POST['titre'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $date_debut     = $_POST['date_debut'] ?? '';
    $date_fin       = $_POST['date_fin'] ?? $date_debut;
    $lieu           = trim($_POST['lieu'] ?? '');
    $capacite_max   = intval($_POST['capacite_max'] ?? 1);
    $id             = $_POST['id_evenement'] ?? null;

    // Verification PHP 
    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($lieu)) $errors[] = "Le lieu est obligatoire.";
    if (empty($date_debut)) $errors[] = "La date de début est obligatoire.";
    if ($capacite_max <= 0) $errors[] = "La capacité doit être supérieure à 0.";
    if (!empty($date_debut) && !empty($date_fin) && $date_debut > $date_fin) {
        $errors[] = "La date de fin doit être après la date de début.";
    }

    if (empty($errors)) {
        $event = new Event(
            $titre, $description, $date_debut, $date_fin, $lieu,
            $capacite_max, $capacite_max, 1, 'Actif'
        );

        if ($id && is_numeric($id)) {
          
            $eventController->updateEvent($event, $id);
            $success = "Événement modifié avec succès !";
            $editMode = false;
        } else {
            
            $eventController->addEvent($event);
            $success = " Événement publié avec succès !";
        }
        
        header('Location: dashboard.php?tab=events&msg=success' . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($sort) ? '&sort=' . $sort : ''));
        exit();
    }
}

// GESTION DE LA SUPPRESSION 
if (isset($_GET['action']) && $_GET['action'] === 'delete_event' && isset($_GET['id'])) {
    $eventController->deleteEvent($_GET['id']);
    header('Location: dashboard.php?tab=events&msg=deleted' . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($sort) ? '&sort=' . $sort : ''));
    exit();
}

// GESTION DES RESSOURCES 
$searchRessource = $_GET['search_ressource'] ?? '';
$typeFilter = $_GET['type_ressource'] ?? '';

// AJOUT DE RESSOURCE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_ressource']) && $_POST['action_ressource'] === 'add_ressource') {
    
    $nom = trim($_POST['nom'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description_ressource'] ?? '');
    $quantite_disponible = intval($_POST['quantite_disponible'] ?? 0);
    $quantite_totale = intval($_POST['quantite_totale'] ?? 0);
    $etat = trim($_POST['etat'] ?? '');
    $statut = trim($_POST['statut'] ?? 'Disponible');
    $date_achat = $_POST['date_achat'] ?? null;

    if (empty($nom)) $errors[] = "Le nom de la ressource est obligatoire.";
    if ($quantite_disponible < 0) $errors[] = "La quantité disponible ne peut pas être négative.";
    if ($quantite_totale < 0) $errors[] = "La quantité totale ne peut pas être négative.";
    if ($quantite_disponible > $quantite_totale) {
        $errors[] = "La quantité disponible ne peut pas dépasser la quantité totale.";
    }

    if (empty($errors)) {
        $newRessource = new Ressource(
            $nom, $type, $description, $quantite_disponible, $quantite_totale,
            $etat, 1, $date_achat, $statut
        );
        
        $ressourceController->addRessource($newRessource);
        header('Location: dashboard.php?tab=ressources&msg=ressource_created');
        exit();
    }
}

// SUPPRESSION DE RESSOURCE
if (isset($_GET['action']) && $_GET['action'] === 'delete_ressource' && isset($_GET['id'])) {
    $ressourceController->deleteRessource($_GET['id']);
    header('Location: dashboard.php?tab=ressources&msg=ressource_deleted');
    exit();
}

// RÉCUPÉRATION DES DONNÉES


// Onglet actif (par défaut: événements)
$activeTab = $_GET['tab'] ?? 'events';

// Récupérer les événements 
$events = $eventController->listeEvents($search, $sort);

// Récupérer les ressources 
$ressources = $ressourceController->listeRessources($searchRessource, $typeFilter);
$typesRessources = $ressourceController->getTypesRessources();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap Dashboard</title>
<style>

/* === FIX LAYOUT : Sidebar + Contenu === */
* { box-sizing: border-box; }
body { 
    margin: 0; 
    font-family: 'Segoe UI', sans-serif; 
    background: #eef2f7; 
}

/* --- SIDEBAR --- */
.sidebar { 
    width: 220px; 
    background: linear-gradient(180deg, #6a11cb, #2575fc); 
    color: white; 
    height: 100vh; 
    padding: 20px; 
    position: fixed; 
    left: 0;
    top: 0;
    z-index: 100;
    overflow-y: auto;
}
.sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 22px; }
.sidebar a { 
    display: block; 
    color: white; 
    text-decoration: none; 
    margin: 12px 0; 
    padding: 12px 15px; 
    border-radius: 8px; 
    transition: 0.3s; 
    font-weight: 500;
}
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }

/* --- CONTENU PRINCIPAL --- */
.main { 
    margin-left: 220px; 
    padding: 25px; 
    min-height: 100vh;
}

.topbar { 
    background: white; 
    padding: 15px 20px; 
    border-radius: 12px; 
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 25px; 
    flex-wrap: wrap; 
    gap: 15px; 
}
.topbar h3 { margin: 0; color: #333; font-size: 20px; }

/* --- ONGLETS DE NAVIGATION --- */
.tabs { 
    display: flex; 
    gap: 10px; 
    margin-bottom: 25px; 
    border-bottom: 2px solid #ddd; 
    padding-bottom: 10px;
}
.tab-btn { 
    padding: 12px 24px; 
    background: white; 
    border: 2px solid #ddd; 
    border-radius: 8px 8px 0 0; 
    cursor: pointer; 
    font-weight: 600;
    text-decoration: none;
    color: #666;
    transition: 0.3s;
    border-bottom: none;
}
.tab-btn:hover { background: #f8f9fa; color: #333; }
.tab-btn.active { 
    background: #7b2ff7; 
    color: white; 
    border-color: #7b2ff7; 
}

/* --- SECTIONS (onglets) --- */
.section { display: none; }
.section.active { display: block; animation: fadeIn 0.3s; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* --- CARTES & FORMULAIRES --- */
.card { 
    background: white; 
    padding: 25px; 
    border-radius: 15px; 
    box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
    margin-bottom: 25px; 
}
.form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
.form-group { margin-bottom: 18px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #444; font-size: 14px; }
.form-group input, .form-group textarea, .form-group select { 
    width: 100%; 
    padding: 10px 12px; 
    border: 1px solid #ddd; 
    border-radius: 8px; 
    font-size: 14px;
    transition: border 0.2s;
}
.form-group input:focus, .form-group textarea:focus { border-color: #7b2ff7; outline: none; }

.btn-submit { 
    background: #2575fc; 
    border: none; 
    color: white; 
    padding: 12px 24px; 
    border-radius: 8px; 
    cursor: pointer; 
    font-size: 15px; 
    font-weight: 600;
    transition: background 0.2s;
}
.btn-submit:hover { background: #1a5bb5; }

.btn-cancel { 
    background: #6c757d; 
    border: none; 
    color: white; 
    padding: 12px 24px; 
    border-radius: 8px; 
    cursor: pointer; 
    font-size: 15px; 
    text-decoration: none; 
    display: inline-block;
    margin-left: 10px;
}
.btn-cancel:hover { background: #545b62; }

/* --- RECHERCHE & TRI --- */
.search-sort { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.search-sort input[type="text"] { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; min-width: 200px; }
.search-sort select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; }
.search-sort button { background: #7b2ff7; color: white; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-weight: 500; }
.search-sort button:hover { background: #5a1db8; }
.search-sort a.reset { padding: 8px 12px; background: #f1f3f5; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #555; font-size: 13px; cursor: pointer; }
.search-sort a.reset:hover { background: #e9ecef; }

/* --- TABLEAU --- */
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f9fa; color: #555; font-weight: 600; font-size: 14px; }
th a { color: #555; text-decoration: none; display: flex; align-items: center; gap: 5px; }
th a:hover { color: #7b2ff7; }

.btn-delete { background: #ff4b4b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 12px; font-weight: 500; }
.btn-edit { background: #ffc107; color: #333; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 12px; font-weight: 500; margin-right: 6px; }
.btn-delete:hover { background: #e03e3e; }
.btn-edit:hover { background: #e0a800; }

/* --- BADGES STATUT --- */
.badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-danger { background: #f8d7da; color: #721c24; }

/* --- MESSAGES --- */
.error { color: #721c24; background: #f8d7da; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
.success-msg { color: #155724; background: #d4edda; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }

/* --- MODE ÉDITION --- */
.edit-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.edit-header h3 { margin: 0; color: #7b2ff7; }

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    .sidebar { width: 60px; padding: 10px; }
    .sidebar h2, .sidebar a span { display: none; }
    .main { margin-left: 60px; }
    .form-grid { grid-template-columns: 1fr; }
    .tabs { flex-wrap: wrap; }
    .tab-btn { padding: 10px 16px; font-size: 14px; }
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="index.php">🏠 Retour au Site</a>
</div>

<div class="main">

    <div class="topbar">
        <h3>DASHBOARD EVENEMENTS :</h3>
        
        <!-- Onglets de navigation -->
        <div class="tabs">
            <a href="?tab=events" class="tab-btn <?= $activeTab === 'events' ? 'active' : '' ?>">📅 Événements</a>
            <a href="?tab=ressources" class="tab-btn <?= $activeTab === 'ressources' ? 'active' : '' ?>">🛠️ Ressources</a>
        </div>
        
        <div style="display:flex; gap:10px; align-items:center;">
            <span>Bienvenue, Organisateur</span>
            <div style="width:40px; height:40px; background:#ddd; border-radius:50%;"></div>
        </div>
    </div>

    <!-- MESSAGES DE SUCCÈS / ERREUR -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul style="margin:0; padding-left:20px;">
                <?php foreach($errors as $err) echo "<li>" . htmlspecialchars($err) . "</li>"; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success) || isset($_GET['msg'])): ?>
        <div class="success-msg">
            <?php
            if (!empty($success)) {
                echo htmlspecialchars($success);
            } elseif ($_GET['msg'] === 'success') {
                echo "Action effectuée avec succès !";
            } elseif ($_GET['msg'] === 'deleted') {
                echo "🗑️ Événement supprimé avec succès !";
            } elseif ($_GET['msg'] === 'ressource_created') {
                echo "Ressource ajoutée avec succès !";
            } elseif ($_GET['msg'] === 'ressource_deleted') {
                echo " Ressource supprimée avec succès !";
            }
            ?>
        </div>
    <?php endif; ?>

    <!--  SECTION ÉVÉNEMENTS-->

    <div class="section <?= $activeTab === 'events' ? 'active' : '' ?>">

        <!-- FORMULAIRE (AJOUT ou MODIFICATION) -->
        <div class="card">
            <?php if ($editMode && $editEvent): ?>
                <div class="edit-header">
                    <h3>✏️ Modifier l'événement</h3>
                    <a href="dashboard.php?tab=events<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" class="btn-cancel">Annuler</a>
                </div>
            <?php else: ?>
                <h3 style="margin-top:0; color:#7b2ff7;"> Créer un nouvel événement</h3>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action_event" value="1">
                <?php if ($editMode && $editEvent): ?>
                    <input type="hidden" name="id_evenement" value="<?= $editEvent['id_evenement'] ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Titre de l'événement *</label>
                        <input type="text" name="titre" placeholder="Ex: Atelier PHP" 
                               value="<?= htmlspecialchars($_POST['titre'] ?? ($editEvent['titre'] ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Lieu *</label>
                        <input type="text" name="lieu" placeholder="Ex: Salle A, Paris" 
                               value="<?= htmlspecialchars($_POST['lieu'] ?? ($editEvent['lieu'] ?? '')) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="3" placeholder="Détails de l'événement..."><?= htmlspecialchars($_POST['description'] ?? ($editEvent['description'] ?? '')) ?></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Date de début *</label>
                        <input type="datetime-local" name="date_debut" 
                               value="<?= $_POST['date_debut'] ?? ($editEvent['date_debut'] ? date('Y-m-d\TH:i', strtotime($editEvent['date_debut'])) : '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Date de fin</label>
                        <input type="datetime-local" name="date_fin" 
                               value="<?= $_POST['date_fin'] ?? ($editEvent['date_fin'] ? date('Y-m-d\TH:i', strtotime($editEvent['date_fin'])) : '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Capacité maximale *</label>
                    <input type="number" name="capacite_max" min="1" 
                           value="<?= $_POST['capacite_max'] ?? ($editEvent['capacite_max'] ?? '10') ?>">
                </div>
                
                <button type="submit" name="<?= $editMode ? 'update_event' : 'add_event' ?>" class="btn-submit">
                    <?= $editMode ? ' Mettre à jour' : ' Publier l\'événement' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="dashboard.php?tab=events<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" class="btn-cancel">Annuler</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTE DES ÉVÉNEMENTS -->
        <div class="card">
            <h3 style="margin-top:0; color:#7b2ff7;">📋 Mes Événements 
                <?php if (!empty($search)): ?>
                    <small style="font-weight:normal; color:#888;">(Résultats pour "<?= htmlspecialchars($search) ?>")</small>
                <?php endif; ?>
            </h3>
            
            <!-- RECHERCHE + TRI -->
            <form method="GET" class="search-sort" style="margin-bottom: 15px;">
                <input type="hidden" name="tab" value="events">
                <input type="text" name="search" placeholder="Rechercher par titre..." value="<?= htmlspecialchars($search) ?>">
                
                <select name="sort" onchange="this.form.submit()">
                    <option value="">Trier par...</option>
                    <option value="titre" <?= $sort === 'titre' ? 'selected' : '' ?>>Titre alphabetique</option>
                    <option value="date_debut" <?= $sort === 'date_debut' ? 'selected' : '' ?>>Date</option>
                    <option value="lieu" <?= $sort === 'lieu' ? 'selected' : '' ?>>Lieu</option>
                    <option value="capacite_max" <?= $sort === 'capacite_max' ? 'selected' : '' ?>>Capacité</option>
                </select>
                
                <button type="submit">🔍 Chercher</button>
                <?php if (!empty($search) || !empty($sort)): ?>
                    <a href="dashboard.php?tab=events" class="reset">Reset</a>
                <?php endif; ?>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="?tab=events&sort=titre<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Titre <?= $sort === 'titre' ? '🔼' : '' ?></a>
                        </th>
                        <th>Lieu</th>
                        <th>
                            <a href="?tab=events&sort=date_debut<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Date <?= $sort === 'date_debut' ? '🔼' : '' ?></a>
                        </th>
                        <th>Capacité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $e): 
                            $dateAffichee = !empty($e['date_debut']) ? date('d/m/Y H:i', strtotime($e['date_debut'])) : '-';
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($e['titre']) ?></strong><br>
                                <small style="color:#888;"><?= htmlspecialchars(mb_strimwidth($e['description'], 0, 50, "...")) ?></small>
                            </td>
                            <td><?= htmlspecialchars($e['lieu']) ?></td>
                            <td><?= $dateAffichee ?></td>
                            <td><?= (int)$e['capacite_max'] ?></td>
                            <td>
                                <a href="?tab=events&action=edit_event&id=<?= $e['id_evenement'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" class="btn-edit">✏️ Modifier</a>
                                <a href="?tab=events&action=delete_event&id=<?= $e['id_evenement'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" class="btn-delete" onclick="return confirm('Supprimer cet événement ?');">🗑️ Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; color:#888; padding:20px;">
                            <?= !empty($search) ? 'Aucun résultat pour votre recherche.' : 'Aucun événement créé.' ?>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

  
    <!--RESSOURCES -->
  
    <div class="section <?= $activeTab === 'ressources' ? 'active' : '' ?>">

        <!-- formulaire ajout ressource -->
        <div class="card">
            <h3 style="margin-top:0; color:#7b2ff7;"> Ajouter une ressource</h3>
            <form method="POST">
                <input type="hidden" name="action_ressource" value="add_ressource">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom de la ressource *</label>
                        <input type="text" name="nom" placeholder="Ex: Projecteur, Salle A..." required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type">
                            <option value="">Sélectionner...</option>
                            <option value="Matériel">Matériel</option>
                            <option value="Salle">Salle</option>
                            <option value="Équipement">Équipement</option>
                            <option value="Consommable">Consommable</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité disponible *</label>
                        <input type="number" name="quantite_disponible" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>Quantité totale *</label>
                        <input type="number" name="quantite_totale" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>État</label>
                        <select name="etat">
                            <option value="">Sélectionner...</option>
                            <option value="Neuf">Neuf</option>
                            <option value="Bon état">Bon état</option>
                            <option value="Usé">Usé</option>
                            <option value="À réparer">À réparer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select name="statut">
                            <option value="Disponible">Disponible</option>
                            <option value="Réservé">Réservé</option>
                            <option value="Indisponible">Indisponible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date d'achat</label>
                        <input type="date" name="date_achat">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description_ressource" rows="3" placeholder="Description détaillée..."></textarea>
                </div>
                <button type="submit" class="btn-submit"> Enregistrer la ressource</button>
            </form>
        </div>

        <!-- LISTE RESSOURCES -->
        <div class="card">
            <h3 style="margin-top:0; color:#7b2ff7;">📋 Mes Ressources</h3>
            
            <!-- Recherche et Filtre -->
            <form method="GET" class="search-sort" style="margin-bottom: 15px;">
                <input type="hidden" name="tab" value="ressources">
                <input type="text" name="search_ressource" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($searchRessource) ?>">
                <select name="type_ressource">
                    <option value="">Tous les types</option>
                    <?php foreach($typesRessources as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>" <?= $typeFilter === $t ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">🔍 Filtrer</button>
                <?php if (!empty($searchRessource) || !empty($typeFilter)): ?>
                    <a href="dashboard.php?tab=ressources" class="reset">✕ Reset</a>
                <?php endif; ?>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>État</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ressources)): ?>
                        <?php foreach ($ressources as $r): 
                            $badgeClass = 'badge-success';
                            if ($r['statut'] === 'Réservé') $badgeClass = 'badge-warning';
                            elseif ($r['statut'] === 'Indisponible') $badgeClass = 'badge-danger';
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($r['nom']) ?></strong><br>
                                <small style="color:#888;"><?= htmlspecialchars($r['description'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['type'] ?? '-') ?></td>
                            <td>
                                <?= (int)$r['quantite_disponible'] ?> / <?= (int)$r['quantite_totale'] ?>
                                <?php if ($r['quantite_disponible'] == 0): ?>
                                    <span class="badge badge-danger" style="margin-left:5px;">Épuisé</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['etat'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($r['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="?tab=ressources&action=delete_ressource&id=<?= $r['id_ressource'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Supprimer cette ressource ?');">
                                   🗑️ Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; color:#888; padding:30px;">
                                <?= !empty($searchRessource) ? 'Aucune ressource trouvée.' : 'Aucune ressource enregistrée.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>