<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/EventController.php';
require_once __DIR__ . '/controller/RessourceController.php';
session_start();

$eventController = new EventController();
$ressourceController = new RessourceController();

$action = $_GET['action'] ?? 'home';



// Récupérer les ressources 
if ($action === 'getRessources') {
    header('Content-Type: application/json');
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $ressources = $ressourceController->listeRessources($search, $type, '');
    echo json_encode($ressources);
    exit();
}

//Récupérer les détails d'un événement 
if ($action === 'getEventDetails') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? 0;
    $event = $eventController->getEvent($id);
    
    if ($event) {
        // On formate la date pour l'affichage français
        $event['date_formatee'] = !empty($event['date_debut']) ? date('d/m/Y à H:i', strtotime($event['date_debut'])) : 'Non définie';
        $event['date_fin_formatee'] = !empty($event['date_fin']) ? date('d/m/Y à H:i', strtotime($event['date_fin'])) : 'Non définie';
        echo json_encode($event);
    } else {
        echo json_encode(['error' => 'Événement introuvable']);
    }
    exit();
}
//page d'acceuil
$events = $eventController->listeEvents();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillSwap - Échange de compétences</title>
<style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f5f3ff; color: #333; }
    
    /* NAVBAR */
    .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
    .navbar h2 { color: #7b2ff7; margin: 0; }
    .navbar a { margin: 0 12px; text-decoration: none; color: #555; font-weight: 500; transition: color 0.3s; cursor: pointer; }
    .navbar a:hover { color: #7b2ff7; }
    .navbar a.jointure-link { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white !important; padding: 8px 16px; border-radius: 20px; }
    .navbar a.jointure-link:hover { transform: scale(1.05); background: linear-gradient(90deg, #5a1db8, #7c3aed); }
    .navbar button { background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: bold; transition: transform 0.2s; }
    .navbar button:hover { transform: scale(1.05); }

    /* HERO & CONTENT */
    .hero { text-align: center; padding: 80px 20px 120px; background: linear-gradient(135deg, #7b2ff7, #c084fc); color: white; position: relative; }
    .hero h1 { font-size: 48px; margin-bottom: 20px; line-height: 1.2; }
    .hero span { color: #fde68a; }
    .hero p { font-size: 18px; opacity: 0.9; }
    .events-container { max-width: 1000px; margin: -60px auto 50px; padding: 0 20px; position: relative; z-index: 10; }
    .section-title { text-align: left; color: white; font-size: 28px; margin-bottom: 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    
    /* CARDS ÉVÉNEMENTS */
    .event-card { 
        background: white; padding: 25px; margin-bottom: 20px; border-radius: 15px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        display: flex; justify-content: space-between; align-items: center; 
        transition: transform 0.3s, box-shadow 0.3s; 
        cursor: pointer; /* Indique qu'on peut cliquer partout */
    }
    .event-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
    .event-info { flex: 1; }
    .event-info h3 { color: #7b2ff7; margin: 0 0 8px 0; font-size: 22px; }
    .event-info p { color: #666; margin: 5px 0; }
    .event-meta { color: #888; font-size: 14px; font-weight: bold; display: block; margin-top: 10px; }
    
    .btn-details { 
        background: transparent; border: 2px solid #7b2ff7; color: #7b2ff7; 
        text-decoration: none; padding: 8px 20px; border-radius: 25px; 
        font-weight: bold; transition: all 0.3s; margin-left: 20px;
    }
    .btn-details:hover { background: #7b2ff7; color: white; }

    /* SECTIONS */
    .section { padding: 60px 20px; text-align: center; }
    .steps { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-top: 30px; }
    .step { background: white; padding: 30px; border-radius: 20px; width: 250px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .step h3 { color: #7b2ff7; }
    .skills { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-top: 30px; }
    .skill { background: #e7f9ff; color: #007bff; padding: 15px 30px; border-radius: 50px; font-weight: bold; }
    footer { text-align: center; padding: 30px; background: #1a1a2e; color: #ccc; }

    /* --- STYLE GÉNÉRAL DES MODALS --- */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6); z-index: 2000;
        justify-content: center; align-items: center; backdrop-filter: blur(5px);
    }
    .modal-overlay.active { display: flex; animation: fadeIn 0.3s; }
    
    .modal-content {
        background: white; width: 90%; max-width: 800px; max-height: 85vh;
        border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        display: flex; flex-direction: column; overflow: hidden; position: relative;
    }
    
    .modal-header {
        padding: 20px 30px; background: linear-gradient(90deg, #7b2ff7, #a855f7);
        color: white; display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h2 { margin: 0; font-size: 24px; }
    .close-modal { background: none; border: none; color: white; font-size: 30px; cursor: pointer; line-height: 1; }
    
    .modal-body { padding: 30px; overflow-y: auto; }

    /* Style spécifique Modal Ressources */
    .resource-filters { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
    .resource-filters input, .resource-filters select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; }
    .resource-filters button { background: #7b2ff7; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
    .resource-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .resource-item { border: 1px solid #eee; border-radius: 12px; padding: 15px; text-align: center; transition: transform 0.2s; }
    .resource-item:hover { transform: translateY(-3px); border-color: #7b2ff7; }
    .res-icon { font-size: 40px; margin-bottom: 10px; display: block; }
    .res-name { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
    .res-type { font-size: 12px; color: #888; background: #f0f0f0; padding: 4px 8px; border-radius: 4px; display: inline-block; }
    .res-stock { margin-top: 10px; font-size: 13px; color: #28a745; font-weight: 600; }

    /* Style spécifique Modal Détails Événement */
    .detail-row { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .detail-label { font-weight: bold; color: #7b2ff7; display: block; margin-bottom: 5px; font-size: 14px; text-transform: uppercase; }
    .detail-value { font-size: 16px; color: #333; line-height: 1.6; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .btn-register-big { 
        display: block; width: 70%; text-align: center; background: #c6c6c6; color:white; 
        padding: 15px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 18px; margin-top: 20px;
    }
    .btn-register-big:hover { background: #ffffff; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
</head>
<body>

<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="index.php">Accueil</a>
        <a href="#">Explorer</a>
        <a href="#" onclick="openResourceModal(event)" class="jointure-link">🔗 Ressources</a>
        <a href="dashboard.php">Espace Organisateur</a>
        <button>Commencer</button>
    </div>
</div>

<div class="hero">
    <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
    <p>Rejoins la communauté et apprends de nouvelles choses !</p>
</div>

<div class="events-container">
    <h2 class="section-title">-- Événements à venir --</h2>
    <?php 
    if (!empty($events)) {
        foreach ($events as $e) {
            $dateAffichee = !empty($e['date_debut']) ? date('d/m/Y', strtotime($e['date_debut'])) : 'Date non définie';
          
            echo '
            <div class="event-card" onclick="openEventDetails(' . $e['id_evenement'] . ')">
                <div class="event-info">
                    <h3>' . htmlspecialchars($e['titre']) . '</h3>
                    <p>' . htmlspecialchars(mb_strimwidth($e['description'], 0, 80, "...")) . '</p>
                    <span class="event-meta">📍 ' . htmlspecialchars($e['lieu']) . ' | 🕒 ' . $dateAffichee . '</span>
                </div>
                <button class="btn-details" onclick="event.stopPropagation(); openEventDetails(' . $e['id_evenement'] . ')">Voir détails</button>
            </div>';
        }
    } else {
        echo '<div class="event-card" style="justify-content:center; color:#888;">Aucun événement prévu pour le moment.</div>';
    }
    ?>
</div>

<div class="section">
    <h2>Comment ça marche ?</h2>
    <div class="steps">
        <div class="step"><h3>1. Ajoute</h3><p>Ajoute tes compétences</p></div>
        <div class="step"><h3>2. Trouve</h3><p>Cherche un échange</p></div>
        <div class="step"><h3>3. Collabore</h3><p>Travaille et apprends</p></div>
    </div>
</div>

<div class="section" style="background: white;">
    <h2>Compétences populaires</h2>
    <div class="skills">
        <div class="skill">Développement Web</div>
        <div class="skill">Design Graphique</div>
        <div class="skill">Montage Vidéo</div>
        <div class="skill">Marketing Digital</div>
    </div>
</div>

<footer>
    <p>© 2026 SkillSwap - Tous droits réservés</p>
</footer>

<div class="modal-overlay" id="resourceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🛠️ Nos Ressources Disponibles</h2>
            <button class="close-modal" onclick="closeModal('resourceModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="resource-filters">
                <input type="text" id="resSearch" placeholder="Rechercher...">
                <select id="resType">
                    <option value="">Tous les types</option>
                    <option value="Matériel">Matériel</option>
                    <option value="Salle">Salle</option>
                    <option value="Équipement">Équipement</option>
                </select>
                <button onclick="loadResources()">🔍 Filtrer</button>
            </div>
            <div id="resourceGrid" class="resource-grid"></div>
        </div>
    </div>
</div>


<div class="modal-overlay" id="eventDetailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalEventTitle">Détails de l'événement</h2>
            <button class="close-modal" onclick="closeModal('eventDetailModal')">&times;</button>
        </div>
        <div class="modal-body" id="modalEventBody">
          
            <p style="text-align:center;">Chargement...</p>
        </div>
    </div>
</div>

<script>
  
    function openResourceModal(e) {
        if(e) e.preventDefault();
        document.getElementById('resourceModal').classList.add('active');
        loadResources();
    }

    function loadResources() {
        const search = document.getElementById('resSearch').value;
        const type = document.getElementById('resType').value;
        const grid = document.getElementById('resourceGrid');
        grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">Chargement...</p>';

        fetch(`index.php?action=getRessources&search=${encodeURIComponent(search)}&type=${encodeURIComponent(type)}`)
            .then(response => response.json())
            .then(data => {
                grid.innerHTML = '';
                if (data.length === 0) {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align:center; color:#888;">Aucune ressource trouvée.</p>';
                    return;
                }
                data.forEach(res => {
                    let icon = '📦';
                    if(res.type === 'Salle') icon = '🏫';
                    if(res.type === 'Matériel') icon = '💻';
                    if(res.type === 'Consommable') icon = '🔋';
                    if(res.type === 'Autre') icon = '🖊️';
                    
                    const item = document.createElement('div');
                    item.className = 'resource-item';
                    item.innerHTML = `
                        <span class="res-icon">${icon}</span>
                        <span class="res-name">${res.nom}</span>
                        <span class="res-type">${res.type || 'Divers'}</span>
                        <div class="res-stock">Dispo: ${res.quantite_disponible}</div>
                    `;
                    grid.appendChild(item);
                });
            });
    }

  
    function openEventDetails(id) {
        const modal = document.getElementById('eventDetailModal');
        const body = document.getElementById('modalEventBody');
        const title = document.getElementById('modalEventTitle');
        
        modal.classList.add('active');
        body.innerHTML = '<p style="text-align:center; padding:20px;">Chargement des détails...</p>';
        title.textContent = "Chargement...";

        fetch(`index.php?action=getEventDetails&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    body.innerHTML = `<p style="color:red; text-align:center;">${data.error}</p>`;
                    return;
                }

                title.textContent = data.titre;
                body.innerHTML = `
                    <div class="detail-grid">
                        <div class="detail-row">
                            <span class="detail-label">📅 Date de début</span>
                            <span class="detail-value">${data.date_formatee}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">🏁 Date de fin</span>
                            <span class="detail-value">${data.date_fin_formatee}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">📍 Lieu</span>
                            <span class="detail-value">${data.lieu}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">👥 Capacité</span>
                            <span class="detail-value">${data.capacite_max} personnes</span>
                        </div>
                    </div>
                    
                    <div class="detail-row" style="border:none;">
                        <span class="detail-label">📝 Description complète</span>
                        <div class="detail-value" style="white-space: pre-line;">${data.description}</div>
                    </div>

                    <a href="?action=register&id=${data.id_evenement}" class="btn-register-big"> S'inscrire à cet événement</a>
                `;
            })
            .catch(err => {
                body.innerHTML = '<p style="color:red; text-align:center;">Erreur de chargement.</p>';
            });
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Fermer en cliquant en dehors
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
        }
    }
</script>

</body>
</html>