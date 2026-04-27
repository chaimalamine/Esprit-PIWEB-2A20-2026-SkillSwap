<?php
require_once 'config.php';
require_once 'model/Event.php';
require_once 'model/Ressource.php';
require_once 'controller/EventController.php';
require_once 'controller/RessourceController.php';

session_start();
$eventController = new EventController();
$ressourceController = new RessourceController();

// INITIALISATION
$errors = []; $fieldErrors = []; $success = '';
$editModeEvent = false; $editEvent = null;
$editModeRessource = false; $editRessource = null;

$searchEvent = $_GET['search'] ?? ''; $sortEvent = $_GET['sort'] ?? '';
$searchRessource = $_GET['search_ressource'] ?? ''; $typeFilter = $_GET['type_ressource'] ?? '';
$sortRessource = $_GET['sort_ressource'] ?? '';

// ÉDITION
if (isset($_GET['action']) && $_GET['action'] === 'edit_event' && isset($_GET['id'])) {
    $editEvent = $eventController->getEvent($_GET['id']);
    if ($editEvent) $editModeEvent = true;
}
if (isset($_GET['action']) && $_GET['action'] === 'edit_ressource' && isset($_GET['id'])) {
    $editRessource = $ressourceController->getRessource($_GET['id']);
    if ($editRessource) $editModeRessource = true;
}

// VALIDATION ÉVÉNEMENTS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_event'])) {
    $titre = trim($_POST['titre'] ?? ''); 
    $description = trim($_POST['description'] ?? '');
    
    // Gest specifique du Lieu (Select + Input Autre)

    $lieu_select = $_POST['lieu_select'] ?? '';
    $lieu_autre = trim($_POST['lieu_autre'] ?? '');
    $lieu = ($lieu_select === 'autre') ? $lieu_autre : $lieu_select;
    $date_debut = trim($_POST['date_debut'] ?? '');
    $date_fin = trim($_POST['date_fin'] ?? $date_debut);
    $capacite_max = trim($_POST['capacite_max'] ?? '');
    $id = $_POST['id_evenement'] ?? null;

    if (empty($titre)) $fieldErrors['titre'] = "Le titre est obligatoire.";
    if (empty($description)) $fieldErrors['description'] = "La description est obligatoire.";
    if (empty($lieu)) $fieldErrors['lieu'] = "Le lieu est obligatoire.";
    
    // Validation dates 
    if (empty($date_debut)) $fieldErrors['date_debut'] = "La date de début est obligatoire.";
    if (!empty($date_fin) && $date_fin < $date_debut) $fieldErrors['date_fin'] = "La date de fin doit être après le début.";
    
    if (empty($capacite_max) || !ctype_digit($capacite_max) || (int)$capacite_max <= 0) $fieldErrors['capacite_max'] = "Entier positif requis.";
    $capacite_max = (int)$capacite_max;

    if (empty($fieldErrors) && empty($errors)) {
        // Conversion format HTML  vers SQL  si nécessaire pour le Model 
        $date_debut_sql = str_replace('T', ' ', $date_debut);
        $date_fin_sql = str_replace('T', ' ', $date_fin);

        $event = new Event($titre, $description, $date_debut_sql, $date_fin_sql, $lieu, $capacite_max, $capacite_max, $_SESSION['user_id'] ?? 1, 'Actif');
        if ($id && is_numeric($id)) { 
            $eventController->updateEvent($event, $id); $success = "Événement modifié !";
             $editModeEvent = false;
        } else { 
            $eventController->addEvent($event);
             $success = "Événement publié !"; 
        }
        header('Location: dashboard.php?tab=events&msg=success' . (!empty($searchEvent) ? '&search=' . urlencode($searchEvent) : '') . (!empty($sortEvent) ? '&sort=' . $sortEvent : ''));
        exit();
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'delete_event' && isset($_GET['id'])) {
    $eventController->deleteEvent($_GET['id']);
    header('Location: dashboard.php?tab=events&msg=deleted');
    exit();
}

// VALIDATION RESSOURCES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_ressource'])) {
    $nom = trim($_POST['nom'] ?? '');
     $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description_ressource'] ?? '');
    $quantite_disponible = trim($_POST['quantite_disponible'] ?? ''); 
    $quantite_totale = trim($_POST['quantite_totale'] ?? '');
    $etat = trim($_POST['etat'] ?? '');
     $statut = trim($_POST['statut'] ?? 'Disponible');
    $date_achat = trim($_POST['date_achat'] ?? ''); 
    $id = $_POST['id_ressource'] ?? null;

    if (empty($nom)) $fieldErrors['nom'] = "Le nom est obligatoire.";
    if (empty($quantite_disponible) || !ctype_digit($quantite_disponible) || (int)$quantite_disponible < 0)
         $fieldErrors['quantite_disponible'] = "Entier >= 0 requis.";
    if (empty($quantite_totale) || !ctype_digit($quantite_totale) || (int)$quantite_totale < 0)
         $fieldErrors['quantite_totale'] = "Entier >= 0 requis.";
    $quantite_disponible = (int)$quantite_disponible;
     $quantite_totale = (int)$quantite_totale;
    if ($quantite_disponible > $quantite_totale) $errors[] = "Qté dispo > Qté totale.";
    
    if (empty($fieldErrors) && empty($errors)) {
        $newRessource = new Ressource($nom, $type, $description, $quantite_disponible, $quantite_totale, $etat, $_SESSION['user_id'] ?? 1, $date_achat, $statut);
        if ($id && is_numeric($id)) { $ressourceController->updateRessource($newRessource, $id);
         $success = "Ressource modifiée !";
          }
        else { $ressourceController->addRessource($newRessource); 
        $success = "Ressource ajoutée !"; 
        }
        $editModeRessource = false;
        header('Location: dashboard.php?tab=ressources&msg=updated');
        exit();
    }
}
if (isset($_GET['action']) && $_GET['action'] === 'delete_ressource' && isset($_GET['id'])) {
    $ressourceController->deleteRessource($_GET['id']);
    header('Location: dashboard.php?tab=ressources&msg=deleted');
    exit();
}

// GESTION DES ASSOCIATIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_associer'])) { //On récupère les valeurs envoyées par le formulaire HTML
    $id_evenement = $_POST['id_evenement'] ?? null;
    $id_ressource = $_POST['id_ressource'] ?? null;
    $quantite = max(1, (int)($_POST['quantite_utilisee'] ?? 1)); //forces val min a 1
    $statut = $_POST['statut_reservation'] ?? 'En attente';
    
    if ($id_evenement && $id_ressource) {
        $res = $ressourceController->getRessource($id_ressource);
        if ($res && $res['quantite_disponible'] >= $quantite) {
            // enregistr dans la bbd 
            $db = config::getConnexion();
            try {
                $req = $db->prepare('INSERT INTO evenement_ressource (id_evenement, id_ressource, quantite_utilisee, statut_reservation) VALUES (:idEvent, :idRess, :quantite, :statut) ON DUPLICATE KEY UPDATE quantite_utilisee = :quantite, statut_reservation = :statut');
                $req->execute(['idEvent' => $id_evenement,
                 'idRess' => $id_ressource,
                  'quantite' => $quantite, 
                  'statut' => $statut]);
                $success = "✅ Ressource associée avec succès !";
                $activeAssocEventId = $id_evenement; // Pour rouvrir le modal après (enrg id dans active..)
            } catch (Exception $e) {
                 $errors[] = "Erreur Base de donnees " . $e->getMessage(); 
                 }
        } else {
            $errors[] = "Stock insuffisant ou ressource invalide.";
        }
    } else {
        $errors[] = "Veuillez sélectionner une ressource.";
    }
}

// Dissociation
if (isset($_GET['action']) && $_GET['action'] === 'dissocier' && isset($_GET['id_evenement']) && isset($_GET['id_ressource'])) {
    $db = config::getConnexion();
    try {
        $req = $db->prepare('DELETE FROM evenement_ressource WHERE id_evenement = :idEvent AND id_ressource = :idRess');
        $req->execute(['idEvent' => $_GET['id_evenement'], 'idRess' => $_GET['id_ressource']]);
        header('Location: dashboard.php?tab=events&assoc_event_id='.$_GET['id_evenement'].'&msg=dissociated');
        exit();
    } catch (Exception $e) { 
        $errors[] = "Erreur: " . $e->getMessage();
         }
}

// lister les donnees 
$activeTab = $_GET['tab'] ?? 'events';
$events = $eventController->listeEvents($searchEvent, $sortEvent);  //lister les evenements
$ressources = $ressourceController->listeRessources($searchRessource, $typeFilter, $sortRessource);
$typesRessources = $ressourceController->getTypesRessources();

// Précharger TOUTES les associations pour affichage instantané dans le modal
$allAssociations = [];
try {
    $db = config::getConnexion();
    $stmt = $db->query("SELECT er.id_evenement, r.id_ressource, r.nom, r.type, r.quantite_disponible, er.quantite_utilisee, er.statut_reservation FROM evenement_ressource er JOIN ressource r ON er.id_ressource = r.id_ressource");
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $allAssociations[$row['id_evenement']][] = $row;
    }
} catch(Exception $e) { /* Table vide ou inexistante */ }

$activeAssocEventId = $_GET['assoc_event_id'] ?? null;

function showFieldError($f) { 
    global $fieldErrors; 
    return isset($fieldErrors[$f]) ? '<div class="field-error">'.htmlspecialchars($fieldErrors[$f]).'</div>' : '';
     }
function inputClass($f) { 
    global $fieldErrors;
     return isset($fieldErrors[$f]) ? ' error-input' : '';
      }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap Dashboard</title>
<style>
*{box-sizing:border-box;}body{margin:0;font-family:'Segoe UI',sans-serif;background:#eef2f7;}
.sidebar{width:220px;background:linear-gradient(180deg,#6a11cb,#2575fc);color:white;height:100vh;padding:20px;position:fixed;left:0;top:0;z-index:100;overflow-y:auto;}
.sidebar h2{text-align:center;margin-bottom:30px;font-size:22px;}
.sidebar a{display:block;color:white;text-decoration:none;margin:12px 0;padding:12px 15px;border-radius:8px;transition:0.3s;font-weight:500;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{margin-left:220px;padding:25px;min-height:100vh;}
.topbar{background:white;padding:15px 20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:15px;}
.topbar h3{margin:0;color:#333;font-size:20px;}
.tabs{display:flex;gap:10px;margin-bottom:25px;border-bottom:2px solid #ddd;padding-bottom:10px;}
.tab-btn{padding:12px 24px;background:white;border:2px solid #ddd;border-radius:8px 8px 0 0;cursor:pointer;font-weight:600;text-decoration:none;color:#666;transition:0.3s;border-bottom:none;}
.tab-btn:hover{background:#f8f9fa;color:#333;}
.tab-btn.active{background:#7b2ff7;color:white;border-color:#7b2ff7;}
.section{display:none;}.section.active{display:block;animation:fadeIn 0.3s;}@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
.card{background:white;padding:25px;border-radius:15px;box-shadow:0 2px 12px rgba(0,0,0,0.08);margin-bottom:25px;}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:15px;}
.form-group{margin-bottom:18px;position:relative;}
.form-group label{display:block;margin-bottom:6px;font-weight:600;color:#444;font-size:14px;}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;transition:border 0.2s;}
.form-group input.error-input,.form-group textarea.error-input,.form-group select.error-input{border-color:#dc3545;background-color:#fff5f5;}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:#7b2ff7;outline:none;}
.field-error{color:#dc3545;font-size:12px;margin-top:4px;font-weight:500;}
.btn-submit{background:#2575fc;border:none;color:white;padding:12px 24px;border-radius:8px;cursor:pointer;font-size:15px;font-weight:600;transition:background 0.2s;}
.btn-submit:hover{background:#1a5bb5;}
.btn-cancel{background:#6c757d;border:none;color:white;padding:12px 24px;border-radius:8px;cursor:pointer;font-size:15px;text-decoration:none;display:inline-block;margin-left:10px;}
.btn-cancel:hover{background:#545b62;}
.search-sort{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:15px;}
.search-sort input[type="text"]{padding:8px 12px;border:1px solid #ddd;border-radius:8px;min-width:200px;}
.search-sort select{padding:8px 12px;border:1px solid #ddd;border-radius:8px;}
.search-sort button{background:#7b2ff7;color:white;border:none;padding:9px 16px;border-radius:8px;cursor:pointer;font-weight:500;}
.search-sort a.reset{padding:8px 12px;background:#f1f3f5;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#555;font-size:13px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{padding:14px 12px;text-align:left;border-bottom:1px solid #eee;}
th{background:#f8f9fa;color:#555;font-weight:600;font-size:14px;}th a{color:#555;text-decoration:none;}th a:hover{color:#7b2ff7;}
.btn-delete{background:#ff4b4b;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;text-decoration:none;font-size:12px;font-weight:500;}
.btn-edit{background:#ffc107;color:#333;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;text-decoration:none;font-size:12px;font-weight:500;margin-right:6px;}
.btn-jointure{background:#28a745;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;text-decoration:none;font-size:12px;font-weight:500;margin-right:6px;}
.btn-delete:hover{background:#e03e3e;}.btn-edit:hover{background:#e0a800;}.btn-jointure:hover{background:#218838;}
.badge{padding:4px 10px;border-radius:12px;font-size:12px;font-weight:500;}.badge-success{background:#d4edda;color:#155724;}.badge-warning{background:#fff3cd;color:#856404;}.badge-danger{background:#f8d7da;color:#721c24;}
.error{color:#721c24;background:#f8d7da;padding:12px;border-radius:8px;margin-bottom:20px;}
.success-msg{color:#155724;background:#d4edda;padding:12px;border-radius:8px;margin-bottom:20px;}
.edit-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}.edit-header h3{margin:0;color:#7b2ff7;}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;}
.modal-overlay.active{display:flex;}.modal{background:white;padding:25px;border-radius:12px;max-width:700px;width:95%;box-shadow:0 10px 40px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto;}
.modal h4{margin:0 0 15px 0;color:#333;}.modal-buttons{display:flex;gap:10px;justify-content:center;margin-top:20px;}
.modal-btn{padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:500;}.modal-btn.cancel{background:#6c757d;color:white;}.modal-btn.confirm{background:#2575fc;color:white;}.modal-btn:hover{opacity:0.9;}
.assoc-list{margin-top:20px;border-top:1px solid #eee;padding-top:15px;}
.assoc-list table{font-size:13px;margin-top:10px;}
.assoc-list th{background:#f1f3f5;}
@media(max-width:768px){.sidebar{width:60px;padding:10px;}.sidebar h2,.sidebar a span{display:none;}.main{margin-left:60px;}.form-grid{grid-template-columns:1fr;}.tabs{flex-wrap:wrap;}.tab-btn{padding:10px 16px;font-size:14px;}}
</style>
</head>
<body>
<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="dashboard.php?tab=events">📅 Événements</a>
    <a href="dashboard.php?tab=ressources">🛠️ Ressources</a>
    <a href="index.php">🏠 Retour au Site</a>
</div>

<div class="main">
    <div class="topbar">
        <h3>🎛️ GESTION :</h3>
        <div class="tabs">
            <a href="?tab=events" class="tab-btn <?= $activeTab==='events'?'active':'' ?>">📅 Événements</a>
            <a href="?tab=ressources" class="tab-btn <?= $activeTab==='ressources'?'active':'' ?>">🛠️ Ressources</a>
        </div>
        <div style="display:flex;gap:10px;align-items:center;"><span>Organisateur</span><div style="width:40px;height:40px;background:#ddd;border-radius:50%;"></div></div>
    </div>

    <?php if(!empty($errors)): ?><div class="error"><ul style="margin:0;padding-left:20px;"><?php foreach($errors as $err) echo "<li>".htmlspecialchars($err)."</li>"; ?></ul></div><?php endif; ?>
    <?php if(!empty($success)||isset($_GET['msg'])): ?><div class="success-msg"><?php
        if(!empty($success)) echo htmlspecialchars($success);
        elseif($_GET['msg']==='deleted') echo "🗑️ Supprimé avec succès !";
        elseif($_GET['msg']==='updated') echo "💾 Enregistré avec succès !";
        elseif($_GET['msg']==='associated') echo "✅ Ressource associée !";
        elseif($_GET['msg']==='dissociated') echo "✅ Ressource dissociée !";
        else echo "✅ Action effectuée !";
    ?></div><?php endif; ?>

    <!-- ÉVÉNEMENTS -->
    <div class="section <?= $activeTab==='events'?'active':'' ?>">
        <div class="card">
            <?php if($editModeEvent && $editEvent): ?>
                <div class="edit-header"><h3>✏️ Modifier l'événement</h3><a href="?tab=events<?= !empty($searchEvent)?'&search='.urlencode($searchEvent):'' ?><?= !empty($sortEvent)?'&sort='.$sortEvent:'' ?>" class="btn-cancel">Annuler</a></div>
            <?php else: ?><h3 style="margin-top:0;color:#7b2ff7;">➕ Créer un événement</h3><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action_event" value="1">
                <?php if($editModeEvent && $editEvent): ?><input type="hidden" name="id_evenement" value="<?= $editEvent['id_evenement'] ?>"><?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group"><label>Titre *</label><input type="text" name="titre" class="<?=inputClass('titre')?>" value="<?=htmlspecialchars($_POST['titre']??($editEvent['titre']??''))?>"><?=showFieldError('titre')?></div>
                    
                    
                    <div class="form-group">
                        <label>Lieu *</label>
                        <?php 
                            // Déterminer la valeur sélectionnée précédemment
                            $prevLieu = $_POST['lieu_select'] ?? ($editEvent['lieu'] ?? '');
                            $isAutre = ($prevLieu !== 'En ligne');
                            $lieuValue = $isAutre ? htmlspecialchars($_POST['lieu_autre'] ?? ($editEvent['lieu'] ?? '')) : '';
                        ?>
                        <select name="lieu_select" id="lieu_select" class="<?=inputClass('lieu')?>" onchange="toggleLieuInput()" required>
                            <option value="">-- Choisir --</option>
                            <option value="En ligne" <?= $prevLieu === 'En ligne' ? 'selected' : '' ?>>En ligne</option>
                            <option value="autre" <?= $isAutre ? 'selected' : '' ?>>Autre (Saisir manuellement)</option>
                        </select>
                        <input type="text" name="lieu_autre" id="lieu_autre" placeholder="Précisez le lieu..." 
                               value="<?= $lieuValue ?>" 
                               style="margin-top:5px; display: <?= $isAutre ? 'block' : 'none' ?>;">
                        <?=showFieldError('lieu')?>
                    </div>
                </div>

                <div class="form-group"><label>Description *</label><textarea name="description" rows="3" class="<?=inputClass('description')?>"><?=htmlspecialchars($_POST['description']??($editEvent['description']??''))?></textarea><?=showFieldError('description')?></div>
                
                <!-- MODIFICATION DATES : Type datetime-local -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Date début *</label>
                        <input type="datetime-local" name="date_debut" class="<?=inputClass('date_debut')?>" 
                               value="<?= isset($_POST['date_debut']) ? htmlspecialchars($_POST['date_debut']) : (!empty($editEvent['date_debut']) ? date('Y-m-d\TH:i', strtotime($editEvent['date_debut'])) : '') ?>" 
                               required>
                        <?=showFieldError('date_debut')?>
                    </div>
                    <div class="form-group">
                        <label>Date fin</label>
                        <input type="datetime-local" name="date_fin" class="<?=inputClass('date_fin')?>" 
                               value="<?= isset($_POST['date_fin']) ? htmlspecialchars($_POST['date_fin']) : (!empty($editEvent['date_fin']) ? date('Y-m-d\TH:i', strtotime($editEvent['date_fin'])) : '') ?>">
                        <?=showFieldError('date_fin')?>
                    </div>
                </div>

                <div class="form-group"><label>Capacité *</label><input type="text" name="capacite_max" class="<?=inputClass('capacite_max')?>" value="<?=htmlspecialchars($_POST['capacite_max']??($editEvent['capacite_max']??'10'))?>"><?=showFieldError('capacite_max')?></div>
                <button type="submit" name="<?= $editModeEvent?'update_event':'add_event' ?>" class="btn-submit"><?= $editModeEvent?'💾 Mettre à jour':'🚀 Publier' ?></button>
                <?php if($editModeEvent): ?><a href="?tab=events<?= !empty($searchEvent)?'&search='.urlencode($searchEvent):'' ?><?= !empty($sortEvent)?'&sort='.$sortEvent:'' ?>" class="btn-cancel">Annuler</a><?php endif; ?>
            </form>
        </div>
        <div class="card">
            <h3 style="margin-top:0;color:#7b2ff7;">📋 Mes Événements</h3>
            <form method="GET" class="search-sort"><input type="hidden" name="tab" value="events"><input type="text" name="search" placeholder="Rechercher..." value="<?=htmlspecialchars($searchEvent)?>"><select name="sort"><option value="">Trier par...</option><option value="titre" <?=$sortEvent==='titre'?'selected':''?>>Titre</option><option value="date_debut" <?=$sortEvent==='date_debut'?'selected':''?>>Date</option><option value="lieu" <?=$sortEvent==='lieu'?'selected':''?>>Lieu</option></select><button type="submit">🔍</button><?php if(!empty($searchEvent)):?><a href="?tab=events" class="reset">✕</a><?php endif; ?></form>
            <table><thead><tr><th>Titre</th><th>Lieu</th><th>Date</th><th>Capacité</th><th>Actions</th></tr></thead><tbody>
            <?php if(!empty($events)): foreach($events as $e): 
                $dateAff=!empty($e['date_debut'])?date('d/m/Y H:i',strtotime($e['date_debut'])):'-'; 
                $nbAssoc = isset($allAssociations[$e['id_evenement']]) ? count($allAssociations[$e['id_evenement']]) : 0;
            ?>
            <tr>
                <td><strong><?=htmlspecialchars($e['titre'])?></strong></td>
                <td><?=htmlspecialchars($e['lieu'])?></td>
                <td><?=$dateAff?></td>
                <td><?= (int)$e['capacite_max'] ?></td>
                <td>
                    <button type="button" class="btn-jointure" onclick="openAssocModal(<?= (int)$e['id_evenement'] ?>, '<?= addslashes($e['titre']) ?>')">🔗 Associer (<?= $nbAssoc ?>)</button>
                    <a href="#" class="btn-edit" onclick="openEditModal('?tab=events&action=edit_event&id=<?= $e['id_evenement'] ?><?= !empty($searchEvent)?'&search='.urlencode($searchEvent):'' ?><?= !empty($sortEvent)?'&sort='.$sortEvent:'' ?>');return false;">✏️</a>
                    <a href="#" class="btn-delete" onclick="openDeleteModal('?tab=events&action=delete_event&id=<?= $e['id_evenement'] ?><?= !empty($searchEvent)?'&search='.urlencode($searchEvent):'' ?><?= !empty($sortEvent)?'&sort='.$sortEvent:'' ?>','événement');return false;">🗑️</a>
                </td>
            </tr>
            <?php endforeach;
         else: ?><tr><td colspan="5" style="text-align:center;color:#888;padding:20px;">Aucun événement</td></tr><?php endif; ?>
            </tbody></table>
        </div>
    </div>

    <!-- RESSOURCES -->
    <div class="section <?= $activeTab==='ressources'?'active':'' ?>">
        <div class="card">
            <?php if($editModeRessource && $editRessource): ?>
                <div class="edit-header"><h3>✏️ Modifier la ressource</h3><a href="?tab=ressources<?= !empty($searchRessource)?'&search_ressource='.urlencode($searchRessource):'' ?><?= !empty($typeFilter)?'&type_ressource='.urlencode($typeFilter):'' ?><?= !empty($sortRessource)?'&sort_ressource='.$sortRessource:'' ?>" class="btn-cancel">Annuler</a></div>
            <?php else: ?><h3 style="margin-top:0;color:#7b2ff7;">➕ Ajouter une ressource</h3><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action_ressource" value="1">
                <?php if($editModeRessource && $editRessource): ?><input type="hidden" name="id_ressource" value="<?= $editRessource['id_ressource'] ?>"><?php endif; ?>
                <div class="form-grid">
                    <div class="form-group"><label>Nom *</label>
                    <input type="text" name="nom" class="<?=inputClass('nom')?>" value="<?=htmlspecialchars($_POST['nom']??($editRessource['nom']??''))?>"><?=showFieldError('nom')?></div>
                    <div class="form-group"><label>Type</label>
                    <select name="type" class="<?=inputClass('type')?>"><option value="">Sélectionner...</option><option value="Matériel" <?=($_POST['type']??$editRessource['type']??'')==='Matériel'?'selected':''?>>Matériel</option><option value="Salle" <?=($_POST['type']??$editRessource['type']??'')==='Salle'?'selected':''?>>Salle</option><option value="Équipement" <?=($_POST['type']??$editRessource['type']??'')==='Équipement'?'selected':''?>>Équipement</option><option value="Consommable" <?=($_POST['type']??$editRessource['type']??'')==='Consommable'?'selected':''?>>Consommable</option><option value="Autre" <?=($_POST['type']??$editRessource['type']??'')==='Autre'?'selected':''?>>Autre</option></select><?=showFieldError('type')?></div>
                    <div class="form-group"><label>Qté dispo *</label>
                    <input type="text" name="quantite_disponible" class="<?=inputClass('quantite_disponible')?>" value="<?=htmlspecialchars($_POST['quantite_disponible']??($editRessource['quantite_disponible']??'0'))?>"><?=showFieldError('quantite_disponible')?></div>
                    <div class="form-group"><label>Qté totale *</label>
                    <input type="text" name="quantite_totale" class="<?=inputClass('quantite_totale')?>" value="<?=htmlspecialchars($_POST['quantite_totale']??($editRessource['quantite_totale']??'0'))?>"><?=showFieldError('quantite_totale')?></div>
                    <div class="form-group"><label>État</label>
                    <select name="etat" class="<?=inputClass('etat')?>"><option value="">Sélectionner...</option><option value="Neuf" <?=($_POST['etat']??$editRessource['etat']??'')==='Neuf'?'selected':''?>>Neuf</option><option value="Bon état" <?=($_POST['etat']??$editRessource['etat']??'')==='Bon état'?'selected':''?>>Bon état</option><option value="Usé" <?=($_POST['etat']??$editRessource['etat']??'')==='Usé'?'selected':''?>>Usé</option><option value="À réparer" <?=($_POST['etat']??$editRessource['etat']??'')==='À réparer'?'selected':''?>>À réparer</option></select><?=showFieldError('etat')?></div>
                    <div class="form-group"><label>Statut</label>
                    <select name="statut" class="<?=inputClass('statut')?>"><option value="Disponible" <?=($_POST['statut']??$editRessource['statut']??'')==='Disponible'?'selected':''?>>Disponible</option><option value="Réservé" <?=($_POST['statut']??$editRessource['statut']??'')==='Réservé'?'selected':''?>>Réservé</option><option value="Indisponible" <?=($_POST['statut']??$editRessource['statut']??'')==='Indisponible'?'selected':''?>>Indisponible</option></select><?=showFieldError('statut')?></div>                 
                    <div class="form-group">
                    <label>Date d'achat</label>
                        <input type="date" name="date_achat" class="<?=inputClass('date_achat')?>" 
                               value="<?= isset($_POST['date_achat']) ? htmlspecialchars($_POST['date_achat']) : (!empty($editRessource['date_achat']) ? date('Y-m-d', strtotime($editRessource['date_achat'])) : '') ?>">
                        <?=showFieldError('date_achat')?>
                    </div>
                </div>
                <div class="form-group"><label>Description</label><textarea name="description_ressource" rows="3" class="<?=inputClass('description_ressource')?>"><?=htmlspecialchars($_POST['description_ressource']??($editRessource['description']??''))?></textarea><?=showFieldError('description_ressource')?></div>
                <button type="submit" class="btn-submit"><?= $editModeRessource?'💾 Mettre à jour':'💾 Enregistrer' ?></button>
                <?php if($editModeRessource): ?><a href="?tab=ressources<?= !empty($searchRessource)?'&search_ressource='.urlencode($searchRessource):'' ?><?= !empty($typeFilter)?'&type_ressource='.urlencode($typeFilter):'' ?><?= !empty($sortRessource)?'&sort_ressource='.$sortRessource:'' ?>" class="btn-cancel">Annuler</a><?php endif; ?>
            </form>
        </div>
        <div class="card">
            <h3 style="margin-top:0;color:#7b2ff7;">📋 Mes Ressources</h3>
            <form method="GET" class="search-sort"><input type="hidden" name="tab" value="ressources"><input type="text" name="search_ressource" placeholder="Rechercher..." value="<?=htmlspecialchars($searchRessource)?>"><select name="type_ressource"><option value="">Tous types</option><?php foreach($typesRessources as $t): ?><option value="<?=htmlspecialchars($t)?>" <?= $typeFilter===$t?'selected':'' ?>><?=htmlspecialchars($t)?></option><?php endforeach; ?></select><select name="sort_ressource"><option value="">Trier par...</option><option value="nom" <?= $sortRessource==='nom'?'selected':'' ?>>Nom</option><option value="type" <?= $sortRessource==='type'?'selected':'' ?>>Type</option><option value="quantite_disponible" <?= $sortRessource==='quantite_disponible'?'selected':'' ?>>Quantité</option></select><button type="submit">🔍</button><?php if(!empty($searchRessource)||!empty($typeFilter)||!empty($sortRessource)):?><a href="?tab=ressources" class="reset">✕</a><?php endif; ?></form>
            <table><thead><tr><th>Nom</th><th>Type</th><th>Qté</th><th>État</th><th>Statut</th><th>Actions</th></tr></thead><tbody><?php if(!empty($ressources)): foreach($ressources as $r): $badge=$r['statut']==='Disponible'?'badge-success':($r['statut']==='Réservé'?'badge-warning':'badge-danger'); ?><tr><td><strong><?=htmlspecialchars($r['nom'])?></strong></td><td><?=htmlspecialchars($r['type']??'-')?></td><td><?= (int)$r['quantite_disponible'] ?>/<?= (int)$r['quantite_totale'] ?></td><td><?=htmlspecialchars($r['etat']??'-')?></td><td><span class="badge <?= $badge ?>"><?=htmlspecialchars($r['statut'])?></span></td><td><a href="#" class="btn-edit" onclick="openEditModal('?tab=ressources&action=edit_ressource&id=<?= $r['id_ressource'] ?>');return false;">✏️</a><a href="#" class="btn-delete" onclick="openDeleteModal('?tab=ressources&action=delete_ressource&id=<?= $r['id_ressource'] ?>','ressource');return false;">🗑️</a></td></tr><?php endforeach; else: ?><tr><td colspan="6" style="text-align:center;color:#888;padding:30px;">Aucune ressource</td></tr><?php endif; ?></tbody></table>
        </div>
    </div>
</div>

<div class="modal-overlay" id="deleteModal"><div class="modal"><h4>⚠️ Supprimer ?</h4><p id="modalMessage"></p><div class="modal-buttons"><button class="modal-btn cancel" onclick="closeDeleteModal()">Annuler</button><a href="#" class="modal-btn confirm" id="confirmDeleteBtn" style="background:#ff4b4b;color:white;">Supprimer</a></div></div></div>
<div class="modal-overlay" id="editModal"><div class="modal"><h4>✏️ Modifier</h4><p>Vos changements seront enregistrés.</p><div class="modal-buttons"><button class="modal-btn cancel" onclick="closeEditModal()">Annuler</button><a href="#" class="modal-btn confirm" id="confirmEditBtn">Continuer</a></div></div></div>


<div class="modal-overlay" id="assocModal">
    <div class="modal">
        <h4>🔗 Associer une ressource</h4>
        <p id="assocEventTitle" style="margin-bottom:15px;color:#7b2ff7;font-weight:600;"></p>
        
        <form method="POST" id="assocForm">
            <input type="hidden" name="action_associer" value="1">
            <input type="hidden" name="id_evenement" id="assocIdEvenement">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Ressource *</label>
                    <select name="id_ressource" id="assocRessourceSelect" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach($ressourceController->listeRessources('','','') as $r): ?>
                            <option value="<?= (int)$r['id_ressource'] ?>" data-qty="<?= (int)$r['quantite_disponible'] ?>">
                                <?= htmlspecialchars($r['nom']) ?> (<?= htmlspecialchars($r['type']??'') ?>) - Qté: <?= (int)$r['quantite_disponible'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantité *</label>
                    <input type="number" name="quantite_utilisee" id="assocQuantite" min="1" value="1" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut_reservation" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                        <option value="En attente">En attente</option>
                        <option value="Confirmé" selected>Confirmé</option>
                        <option value="Refusé">Refusé</option>
                    </select>
                </div>
            </div>
            <div class="modal-buttons" style="margin-top:15px;">
                <button type="button" class="modal-btn cancel" onclick="closeAssocModal()">Fermer</button>
                <button type="submit" class="modal-btn confirm" style="background:#28a745;">✅ Associer</button>
            </div>
        </form>

        <!-- LISTE DES ASSOCIATIONS EXISTANTES -->
        <div class="assoc-list">
            <h4 style="margin:0 0 10px 0;color:#555;">📋 Déjà associées à cet événement</h4>
            <table id="assocTable"><thead><tr><th>Ressource</th><th>Type</th><th>Qté utilisée</th><th>Statut</th><th>Action</th></tr></thead><tbody id="assocTableBody"><tr><td colspan="5" style="text-align:center;color:#888;padding:15px;">Aucune ressource associée</td></tr></tbody></table>
        </div>
    </div>
</div>

<script>
// Données préchargées depuis PHP (associations)
const assocData = <?= json_encode($allAssociations ?? []) ?>;
let currentAssocEventId = null;

// clic sur associer 

function openAssocModal(id, titre) {
    currentAssocEventId = id;
    document.getElementById('assocIdEvenement').value = id;
    document.getElementById('assocEventTitle').textContent = 'Événement : ' + titre;
    
    // Filtrer le dropdown pour masquer les ressources déjà associées

    const alreadyIds = (assocData[id] || []).map(r => r.id_ressource);
    document.querySelectorAll('#assocRessourceSelect option').forEach(opt => {
        const val = parseInt(opt.value);
        opt.style.display = alreadyIds.includes(val) ? 'none' : '';
        // Mettre à jour la quantité max si ressource sélectionnée
        if (opt.selected) updateQuantiteMax(opt.dataset.qty);
    });
    
    renderAssocTable(id);
    document.getElementById('assocModal').classList.add('active');
}

function closeAssocModal() {
    document.getElementById('assocModal').classList.remove('active');
    currentAssocEventId = null;
}

//la table des assoiciations en bas !!!!!

function renderAssocTable(eventId) {
    const tbody = document.getElementById('assocTableBody');
    const data = assocData[eventId] || [];
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#888;padding:15px;">Aucune ressource associée</td></tr>';
        return;
    }
    
    let html = '';
    data.forEach(r => {
        // Choix de la couleur du badge selon le statut
        const badge = r.statut_reservation === 'Confirmé' ? 'badge-success' : 
                      (r.statut_reservation === 'Refusé' ? 'badge-danger' : 'badge-warning');
        
        // Construction de la ligne du tableau 
        html += `<tr>
            <td><strong>${r.nom}</strong></td>
            <td>${r.type || '-'}</td>
            <td>${r.quantite_utilisee}</td>
            <td><span class="badge ${badge}">${r.statut_reservation}</span></td>
            <td><a href="?action=dissocier&id_evenement=${eventId}&id_ressource=${r.id_ressource}" class="btn-delete" onclick="return confirm('Dissocier cette ressource de l\\'événement ?');">🗑️</a></td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

// Auto-open modal si redirection après association/dissociation
<?php if($activeAssocEventId): 
    $eventTitre = '';
    foreach($events as $ev) { if($ev['id_evenement'] == $activeAssocEventId) { $eventTitre = $ev['titre']; break; } }
?>
window.addEventListener('DOMContentLoaded', () => {
    openAssocModal(<?= (int)$activeAssocEventId ?>, '<?= addslashes($eventTitre) ?>');
});
<?php endif; ?>

// Mise à jour quantité max selon ressource sélectionnée
document.getElementById('assocRessourceSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    updateQuantiteMax(opt.dataset.qty || 0);
});
function updateQuantiteMax(qty) {
    const input = document.getElementById('assocQuantite');
    input.max = qty;
    if (parseInt(input.value) > qty) input.value = qty;
}

// Modals existants
let deleteUrl='';
function openDeleteModal(url,type){
    deleteUrl=url;
    document.getElementById('modalMessage').textContent={'événement':'Voulez-vous vraiment supprimer cet événement ?','ressource':'Voulez-vous vraiment supprimer cette ressource ?'}[type]||'Confirmer ?';
    document.getElementById('confirmDeleteBtn').href=url;
    document.getElementById('deleteModal').classList.add('active');
}
function closeDeleteModal(){
    document.getElementById('deleteModal').classList.remove('active');
}
function openEditModal(url){
    document.getElementById('confirmEditBtn').href=url;
    document.getElementById('editModal').classList.add('active');
}
function closeEditModal(){
    document.getElementById('editModal').classList.remove('active');
}
document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if(e.target===m){m.classList.remove('active');} }));

//lieu
function toggleLieuInput() {
    const select = document.getElementById('lieu_select');
    const input = document.getElementById('lieu_autre');
    if (select.value === 'autre') {
        input.style.display = 'block';
        input.required = true;
    } else {
        input.style.display = 'none';
        input.required = false;
        input.value = ''; // Reset value if switching back to En ligne
    }
}
// Init affichage 
document.addEventListener('DOMContentLoaded', toggleLieuInput);
</script>
</body>
</html>