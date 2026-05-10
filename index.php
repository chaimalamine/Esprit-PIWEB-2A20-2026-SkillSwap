<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/EventController.php';
require_once __DIR__ . '/controller/RessourceController.php';
session_start();

$eventController = new EventController();
$ressourceController = new RessourceController();

// --- TRAITEMENT DES ACTIONS POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_event_feedback'])) {
    $idEvent = intval($_POST['id_evenement'] ?? 0);
    $note = intval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($idEvent > 0 && $note >= 1 && $note <= 5) {
        try {
            $db = config::getConnexion();
            $req = $db->prepare('INSERT INTO feedback_ressource (id_evenement, id_ressource, note, commentaire) VALUES (:evt, NULL, :note, :com)');
            $req->execute([':evt' => $idEvent, ':note' => $note, ':com' => $commentaire]);
            $successMsg = "Merci pour votre avis ! ⭐";
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'envoi.";
        }
    }
}

// --- RÉCUPÉRATION DES DONNÉES ---
$viewMode = $_GET['view'] ?? 'home';
$idEvent = isset($_GET['id']) ? intval($_GET['id']) : 0;

$events = [];
$pastEvents = [];
$eventDetails = null;
$reviews = [];

// Chargement de TOUS les événements pour le Chatbot Global
$allEventsForJS = [];

if ($viewMode === 'home') {
    $events = $eventController->listeEvents();
    
    // Préparer les données pour le JS (Titre, Date, Lieu)
    foreach($events as $e) {
        $allEventsForJS[] = [
            'titre' => $e['titre'],
            'date' => !empty($e['date_debut']) ? date('d/m/Y à H:i', strtotime($e['date_debut'])) : 'Non définie',
            'lieu' => $e['lieu'],
            'lien' => $e['lien_reunion'] ?? '',
            'id' => $e['id_evenement']
        ];
    }

    // Chargement des événements passés notés
    try {
        $db = config::getConnexion();
        $sql = "SELECT e.*, AVG(f.note) as avg_note, COUNT(f.id_feedback) as count_reviews 
                FROM evenement e 
                LEFT JOIN feedback_ressource f ON e.id_evenement = f.id_evenement AND f.id_ressource IS NULL
                WHERE e.date_fin < NOW() 
                GROUP BY e.id_evenement 
                HAVING count_reviews > 0 
                ORDER BY avg_note DESC";
        $stmt = $db->query($sql);
        $pastEvents = $stmt->fetchAll();
    } catch (Exception $e) { $pastEvents = []; }
}

// Chargement des ressources
$resourcesList = [];
if ($viewMode === 'resources') {
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $resourcesList = $ressourceController->listeRessources($search, $type, '');
}

// Chargement des détails d'un événement spécifique
if ($viewMode === 'event_details' && $idEvent > 0) {
    $eventDetails = $eventController->getEvent($idEvent);
    
    if ($eventDetails) {
        $now = new DateTime();
        $fin = !empty($eventDetails['date_fin']) ? new DateTime($eventDetails['date_fin']) : new DateTime($eventDetails['date_debut']);
        $eventDetails['is_past'] = ($now > $fin);
        $eventDetails['date_formatee'] = !empty($eventDetails['date_debut']) ? date('d/m/Y à H:i', strtotime($eventDetails['date_debut'])) : 'Non définie';
        $eventDetails['date_fin_formatee'] = !empty($eventDetails['date_fin']) ? date('d/m/Y à H:i', strtotime($eventDetails['date_fin'])) : 'Non définie';

        if (isset($_GET['show_reviews'])) {
             try {
                $db = config::getConnexion();
                $stmt = $db->prepare('SELECT note, commentaire, date_feedback FROM feedback_ressource WHERE id_evenement = :evt AND id_ressource IS NULL ORDER BY date_feedback DESC');
                $stmt->execute([':evt' => $idEvent]);
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $reviews = []; }
        }
    } else {
        $errorMsg = "Événement introuvable.";
        $viewMode = 'home';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillSwap - Échange de compétences</title>
<style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f5f3ff; color: #333; }
    .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
    .navbar h2 { color: #7b2ff7; margin: 0; }
    .navbar a { margin: 0 12px; text-decoration: none; color: #555; font-weight: 500; transition: color 0.3s; }
    .navbar a:hover, .navbar a.active { color: #7b2ff7; }
    .hero { text-align: center; padding: 80px 20px 120px; background: linear-gradient(135deg, #7b2ff7, #c084fc); color: white; }
    .hero h1 { font-size: 48px; margin-bottom: 20px; }
    .hero span { color: #fde68a; }
    
    .container { max-width: 1000px; margin: -60px auto 50px; padding: 0 20px; position: relative; z-index: 10; }
    .section-title { color: white; font-size: 28px; margin-bottom: 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    
    .card { background: white; padding: 25px; margin-bottom: 20px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s; }
    .card:hover { transform: translateY(-5px); }
    .event-card { display: flex; justify-content: space-between; align-items: center; cursor: pointer; text-decoration: none; color: inherit; }
    .event-info h3 { color: #7b2ff7; margin: 0 0 8px 0; }
    .event-meta { color: #888; font-size: 14px; font-weight: bold; display: block; margin-top: 10px; }
    
    .btn { display: inline-block; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; transition: 0.3s; border: none; cursor: pointer; }
    .btn-primary { background: #7b2ff7; color: white; }
    .btn-primary:hover { background: #5a1db8; }
    .btn-outline { border: 2px solid #7b2ff7; color: #7b2ff7; background: transparent; }
    .btn-outline:hover { background: #7b2ff7; color: white; }
    .btn-zoom { background: #2d8cff; color: white; width: 100%; text-align: center; margin-top: 10px; display: block; }
    
    .section { padding: 60px 20px; text-align: center; }
    .steps { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-top: 30px; }
    .step { background: white; padding: 30px; border-radius: 20px; width: 250px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    
    .top-events-section { background: white; padding: 60px 20px; margin-top: 40px; }
    .top-event-card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 20px; margin-bottom: 15px; max-width: 1000px; margin-left: auto; margin-right: auto; }
    .rank-badge { background: #f39c12; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    
    .resource-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
    .resource-item { border: 1px solid #eee; border-radius: 12px; padding: 15px; text-align: center; background: white; }
    
    /* Details Overlay */
    .details-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
    .details-content { background: white; width: 90%; max-width: 800px; max-height: 90vh; border-radius: 20px; overflow-y: auto; position: relative; padding: 30px; }
    .close-btn { position: absolute; top: 20px; right: 20px; font-size: 30px; text-decoration: none; color: #333; }
    .detail-row { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .detail-label { font-weight: bold; color: #7b2ff7; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 14px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    /* CHATBOT WIDGET FLOTTANT */
    .chat-widget-btn {
        position: fixed; bottom: 30px; left: 30px; width: 60px; height: 60px;
        background: #7b2ff7; border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        display: flex; justify-content: center; align-items: center; cursor: pointer;
        z-index: 3000; transition: transform 0.3s; font-size: 30px; color: white;
    }
    .chat-widget-btn:hover { transform: scale(1.1); }
    
    .chat-window {
        position: fixed; bottom: 100px; left: 30px; width: 350px; height: 450px;
        background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        z-index: 3000; display: none; flex-direction: column; overflow: hidden; border: 1px solid #eee;
    }
    .chat-window.active { display: flex; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .chat-header { background: #7b2ff7; color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
    .close-chat { background: none; border: none; color: white; font-size: 20px; cursor: pointer; }
    
    .chat-messages { flex: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 10px; background: #f9f9f9; }
    .message { max-width: 80%; padding: 10px 15px; border-radius: 15px; font-size: 13px; line-height: 1.4; }
    .msg-bot { background: white; border: 1px solid #eee; align-self: flex-start; border-bottom-left-radius: 2px; }
    .msg-user { background: #7b2ff7; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
    
    .chat-input-area { display: flex; border-top: 1px solid #ddd; background: white; }
    .chat-input-area input { flex: 1; border: none; padding: 15px; outline: none; font-size: 14px; }
    .chat-input-area button { background: white; border: none; color: #7b2ff7; font-weight: bold; padding: 0 15px; cursor: pointer; }
    .chat-link { color: #7b2ff7; text-decoration: underline; font-weight: bold; }

    footer { text-align: center; padding: 30px; background: #1a1a2e; color: #ccc; margin-top: auto; }
    .msg-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; max-width: 1000px; margin: 20px auto; }
</style>
</head>
<body>

<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="index.php" class="<?= $viewMode === 'home' ? 'active' : '' ?>">Accueil</a>
        <a href="index.php?view=resources" class="<?= $viewMode === 'resources' ? 'active' : '' ?>">Ressources</a>
        <a href="dashboard.php">Espace Organisateur</a>
    </div>
</div>

<?php if(isset($successMsg)): ?><div class="msg-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>

<!-- VUE ACCUEIL -->
<?php if($viewMode === 'home'): ?>
    <div class="hero">
        <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
        <p>Rejoins la communauté et apprends de nouvelles choses !</p>
    </div>

    <div class="container">
        <h2 class="section-title">-- Événements à venir --</h2>
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $e): 
                $dateAffichee = !empty($e['date_debut']) ? date('d/m/Y', strtotime($e['date_debut'])) : 'Date non définie';
            ?>
            <a href="index.php?view=event_details&id=<?= $e['id_evenement'] ?>" class="card event-card">
                <div class="event-info">
                    <h3><?= htmlspecialchars($e['titre']) ?></h3>
                    <p><?= htmlspecialchars(mb_strimwidth($e['description'], 0, 80, "...")) ?></p>
                    <span class="event-meta">📍 <?= htmlspecialchars($e['lieu']) ?> | 🕒 <?= $dateAffichee ?></span>
                </div>
                <span class="btn btn-outline">Voir détails</span>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="text-align:center; color:#888;">Aucun événement prévu.</div>
        <?php endif; ?>
    </div>

    <?php if(!empty($pastEvents)): ?>
    <div class="top-events-section">
        <h2 style="color:#333; font-size: 28px; margin-bottom: 30px; text-align:center;">🏆 Les Mieux Notés</h2>
        <?php foreach($pastEvents as $index => $pe): 
            $avg = round($pe['avg_note'], 1);
            $etoiles = ""; for($i=1; $i<=5; $i++) { $etoiles .= ($i <= round($avg)) ? "⭐" : "☆"; }
        ?>
        <div class="top-event-card">
            <div class="rank-badge"><?= $index + 1 ?></div>
            <div style="flex:1; text-align:left;">
                <h3 style="margin:0;"><?= htmlspecialchars($pe['titre']) ?></h3>
                <p style="margin:0; color:#888; font-size:12px;">Terminé le <?= date('d/m/Y', strtotime($pe['date_fin'])) ?></p>
            </div>
            <div style="text-align:right;">
                <div style="color:#f39c12;"><?= $etoiles ?></div>
                <a href="index.php?view=event_details&id=<?= $pe['id_evenement'] ?>&show_reviews=1" class="btn btn-outline" style="font-size:10px; padding:5px;">Avis</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="section">
        <h2>Comment ça marche ?</h2>
        <div class="steps">
            <div class="step"><h3>1. Ajoute</h3><p>Ajoute tes compétences</p></div>
            <div class="step"><h3>2. Trouve</h3><p>Cherche un échange</p></div>
            <div class="step"><h3>3. Collabore</h3><p>Apprends</p></div>
        </div>
    </div>

<!-- VUE RESSOURCES -->
<?php elseif($viewMode === 'resources'): ?>
    <div class="hero" style="padding-bottom: 60px;"><h1>🛠️ Ressources</h1></div>
    <div class="container">
        <form method="GET" style="background:white; padding:20px; border-radius:10px; display:flex; gap:10px; justify-content:center;">
            <input type="hidden" name="view" value="resources">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <select name="type">
                <option value="">Tous</option>
                <option value="Matériel">Matériel</option>
                <option value="Salle">Salle</option>
            </select>
            <button type="submit" class="btn btn-primary">OK</button>
        </form>
        <div class="resource-grid">
            <?php foreach($resourcesList as $res): ?>
                <div class="resource-item">
                    <div style="font-size:30px;">📦</div>
                    <strong><?= htmlspecialchars($res['nom']) ?></strong>
                    <p>Dispo: <?= (int)$res['quantite_disponible'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<!-- VUE DÉTAILS -->
<?php elseif($viewMode === 'event_details' && $eventDetails): ?>
    <div class="details-overlay">
        <div class="details-content">
            <a href="index.php" class="close-btn">&times;</a>
            <h2 style="color:#7b2ff7;"><?= htmlspecialchars($eventDetails['titre']) ?></h2>
            
            <div class="detail-grid">
                <div class="detail-row"><span class="detail-label">📅 Début</span><span><?= $eventDetails['date_formatee'] ?></span></div>
                <div class="detail-row"><span class="detail-label">🏁 Fin</span><span><?= $eventDetails['date_fin_formatee'] ?></span></div>
                <div class="detail-row"><span class="detail-label">📍 Lieu</span><span><?= htmlspecialchars($eventDetails['lieu']) ?></span></div>
                <div class="detail-row"><span class="detail-label">👥 Capacité</span><span><?= (int)$eventDetails['capacite_max'] ?></span></div>
            </div>

            <?php if ($eventDetails['lieu'] === 'En ligne' && !empty($eventDetails['lien_reunion'])): ?>
                <a href="<?= htmlspecialchars($eventDetails['lien_reunion']) ?>" target="_blank" class="btn btn-zoom">🔗 Rejoindre (Zoom)</a>
            <?php elseif (!$eventDetails['is_past']): ?>
                <a href="?action=register&id=<?= $eventDetails['id_evenement'] ?>" class="btn btn-primary" style="width:100%; text-align:center; margin-bottom:20px;">S'inscrire</a>
            <?php endif; ?>

            <div class="detail-row" style="border:none;"><span class="detail-label">📝 Description</span><p><?= nl2br(htmlspecialchars($eventDetails['description'])) ?></p></div>

            <?php if ($eventDetails['is_past']): ?>
                <hr>
                <?php if(isset($_GET['show_rate'])): ?>
                    <form method="POST" style="background:#f9f9f9; padding:20px; border-radius:10px;">
                        <input type="hidden" name="action_event_feedback" value="1">
                        <input type="hidden" name="id_evenement" value="<?= $eventDetails['id_evenement'] ?>">
                        <select name="note" style="font-size:20px;"><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option></select>
                        <textarea name="commentaire" placeholder="Commentaire..." style="width:100%; margin-top:10px;"></textarea>
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Envoyer</button>
                    </form>
                <?php else: ?>
                    <a href="?view=event_details&id=<?= $idEvent ?>&show_rate=1" class="btn btn-primary" style="width:100%; text-align:center;">📝 Noter</a>
                <?php endif; ?>
                
                <?php if(!empty($reviews)): ?>
                    <div style="margin-top:20px;">
                        <h3>Avis</h3>
                        <?php foreach($reviews as $avis): echo "<div style='border-bottom:1px solid #eee; padding:10px;'>".str_repeat('⭐', $avis['note'])." - ".htmlspecialchars($avis['commentaire'])."</div>"; endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<footer><p>© 2026 SkillSwap</p></footer>

<!-- WIDGET CHATBOT -->
<div class="chat-widget-btn" onclick="toggleChat()">💬</div>
<div class="chat-window" id="chatWindow">
    <div class="chat-header"><span>🤖 Assistant SkillSwap</span><button class="close-chat" onclick="toggleChat()">&times;</button></div>
    <div class="chat-messages" id="chatMessages"></div>
    <div class="chat-input-area"><input type="text" id="chatInput" placeholder="Posez une question..."><button id="chatSendBtn">Envoyer</button></div>
</div>

<script>
// --- DONNÉES POUR LE CHATBOT ---
const allEvents = <?= json_encode($allEventsForJS) ?>;
const currentEventId = <?= json_encode($viewMode === 'event_details' ? $idEvent : null) ?>;

const eventDetailsData = {
    titre: "<?= htmlspecialchars($eventDetails['titre'] ?? '') ?>",
    date: "<?= $eventDetails['date_formatee'] ?? '' ?>",
    lieu: "<?= htmlspecialchars($eventDetails['lieu'] ?? '') ?>",
    lien: "<?= htmlspecialchars($eventDetails['lien_reunion'] ?? '') ?>"
};

function toggleChat() {
    document.getElementById('chatWindow').classList.toggle('active');
    setTimeout(() => document.getElementById('chatInput').focus(), 100);
}

const chatInput = document.getElementById('chatInput');
const chatBtn = document.getElementById('chatSendBtn');
const chatMessages = document.getElementById('chatMessages');

function addMessage(text, isUser) {
    const div = document.createElement('div');
    div.className = `message ${isUser ? 'msg-user' : 'msg-bot'}`;
    div.innerHTML = text;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Fonction de recherche améliorée
function findEventInList(query) {
    if (!allEvents || allEvents.length === 0) return null;
    
    // On nettoie la requête : on enlève les mots "c'est", "le", "la", "quand", etc.
    const stopWords = ['c\'est', 'le', 'la', 'les', 'un', 'une', 'quand', 'où', 'lien', 'zoom', 'prix', 'comment'];
    let cleanQuery = query.toLowerCase();
    stopWords.forEach(word => { cleanQuery = cleanQuery.replace(word, ''); });
    
    // On récupère les mots restants (ex: "atelier yoga" -> ["atelier", "yoga"])
    const keywords = cleanQuery.trim().split(/\s+/).filter(w => w.length > 2);

    if (keywords.length === 0) return null;

    // On cherche un événement qui contient AU MOINS UN de ces mots
    for (let event of allEvents) {
        const titleLower = event.titre.toLowerCase();
        // Si le titre contient l'un des mots clés
        if (keywords.some(keyword => titleLower.includes(keyword))) {
            return event;
        }
    }
    
    // Si rien ne correspond, on retourne le premier événement s'il n'y en a qu'un seul
    if (allEvents.length === 1) return allEvents[0];
    
    return null;
}

function getBotResponse(input) {
    const lowerInput = input.toLowerCase();
    let targetEvent = null;
    let contextMsg = "";

    // 1. Identifier l'événement cible
    if (currentEventId) {
        // Mode Détails : on utilise les données chargées
        targetEvent = eventDetailsData;
        contextMsg = ""; 
    } else {
        // Mode Accueil : on cherche dans la liste
        targetEvent = findEventInList(input);
        
        if (targetEvent) {
            contextMsg = `Pour <strong>${targetEvent.titre}</strong> : `;
        } else {
            // Si on ne trouve pas, on donne des indices
            const titles = allEvents.map(e => e.titre).join(', ');
            return `Je n'ai pas trouvé cet événement. Essayez avec l'un de ceux-là : <br><em>${titles}</em>`;
        }
    }

    if (!targetEvent) return "Désolé, je n'ai pas d'informations.";

    // 2. Analyser l'intention (Date, Lieu, Lien)
    
    // LIEN / ZOOM
    if (lowerInput.includes('lien') || lowerInput.includes('zoom') || lowerInput.includes('rejoindre') || lowerInput.includes('url')) {
        if (targetEvent.lien && targetEvent.lien !== '') {
            return `${contextMsg}<br><a href="${targetEvent.lien}" target="_blank" class="chat-link">🔗 Cliquer pour rejoindre</a>`;
        } else {
            return `${contextMsg}Il n'y a pas de lien en ligne pour cet événement.`;
        }
    }
    
    // DATE / HEURE
    if (lowerInput.includes('quand') || lowerInput.includes('date') || lowerInput.includes('heure') || lowerInput.includes('début') || lowerInput.includes('commence')) {
        return `${contextMsg}C'est prévu le <strong>${targetEvent.date}</strong>.`;
    }
    
    // LIEU
    if (lowerInput.includes('où') || lowerInput.includes('lieu') || lowerInput.includes('adresse') || lowerInput.includes('endroit')) {
        if (targetEvent.lieu === 'En ligne') {
            return `${contextMsg}C'est en ligne 🌐. Demandez-moi le 'lien' !`;
        }
        return `${contextMsg}Ça se passe à : <strong>${targetEvent.lieu}</strong>.`;
    }

    // TITRE / GENERAL
    if (lowerInput.includes('quoi') || lowerInput.includes('titre') || lowerInput.includes('c\'est quoi')) {
         return `Il s'agit de : <strong>${targetEvent.titre}</strong>.`;
    }

    // DÉFAUT
    return `Je peux vous dire quand c'est, où c'est, ou vous donner le lien. Essayez : "C'est quand ?"`;
}

if(chatBtn && chatInput) {
    chatBtn.addEventListener('click', () => {
        const text = chatInput.value.trim();
        if (!text) return;
        addMessage(text, true);
        chatInput.value = '';
        setTimeout(() => addMessage(getBotResponse(text), false), 600);
    });
    chatInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') chatBtn.click(); });
    
    // Message d'accueil dynamique
    setTimeout(() => {
        if(allEvents.length > 0) {
            addMessage(`Bonjour ! 👋 Je connais ${allEvents.length} événement(s). Demandez-moi par exemple : "C'est quand le ${allEvents[0].titre} ?"`, false);
        } else {
            addMessage("Bonjour ! Il n'y a pas d'événements pour le moment.", false);
        }
    }, 1000);
}
</script>

</body>
</html>