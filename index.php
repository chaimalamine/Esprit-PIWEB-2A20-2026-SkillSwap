<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/EventController.php';
require_once __DIR__ . '/controller/RessourceController.php';
session_start();

$eventController = new EventController();
$ressourceController = new RessourceController();

// --- TRAITEMENT DES ACTIONS POST ---

// 1. Notation d'un événement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_event_feedback'])) {
    $idEvent = intval($_POST['id_evenement'] ?? 0);
    $note = intval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($idEvent > 0 && $note >= 1 && $note <= 5) {
        try {
            $db = config::getConnexion();
            $req = $db->prepare('INSERT INTO feedback_ressource (id_evenement, id_ressource, note, commentaire) VALUES (:evt, NULL, :note, :com)');
            $req->execute([':evt' => $idEvent, ':note' => $note, ':com' => $commentaire]);
            $successMsg = "Merci pour votre avis sur cet événement ! ⭐";
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'envoi de l'avis.";
        }
    }
}

// --- RÉCUPÉRATION DES DONNÉES ---

// Déterminer quelle vue afficher
$viewMode = $_GET['view'] ?? 'home'; // home, resources, event_details
$idEvent = isset($_GET['id']) ? intval($_GET['id']) : 0;
$idRessourceReview = isset($_GET['review_id']) ? intval($_GET['review_id']) : 0;

$events = [];
$pastEvents = [];
$eventDetails = null;
$reviews = [];

// Chargement des listes principales
if ($viewMode === 'home') {
    $events = $eventController->listeEvents();
    
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

// Chargement des ressources si demandé
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
        // Calculer si c'est passé
        $now = new DateTime();
        $fin = !empty($eventDetails['date_fin']) ? new DateTime($eventDetails['date_fin']) : new DateTime($eventDetails['date_debut']);
        $eventDetails['is_past'] = ($now > $fin);
        
        // Formater les dates
        $eventDetails['date_formatee'] = !empty($eventDetails['date_debut']) ? date('d/m/Y à H:i', strtotime($eventDetails['date_debut'])) : 'Non définie';
        $eventDetails['date_fin_formatee'] = !empty($eventDetails['date_fin']) ? date('d/m/Y à H:i', strtotime($eventDetails['date_fin'])) : 'Non définie';

        // Si on veut voir les avis de cet événement
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
        $viewMode = 'home'; // Retour à l'accueil si erreur
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
    .navbar a.jointure-link { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white !important; padding: 8px 16px; border-radius: 20px; }
    .hero { text-align: center; padding: 80px 20px 120px; background: linear-gradient(135deg, #7b2ff7, #c084fc); color: white; }
    .hero h1 { font-size: 48px; margin-bottom: 20px; }
    .hero span { color: #fde68a; }
    
    .container { max-width: 1000px; margin: -60px auto 50px; padding: 0 20px; position: relative; z-index: 10; }
    .section-title { color: white; font-size: 28px; margin-bottom: 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    
    /* Cards */
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
    .btn-rate { background: #f39c12; color: white; width: 100%; text-align: center; margin-top: 20px; }
    .btn-rate:hover { background: #d35400; }
    .btn-zoom { background: #2d8cff; color: white; width: 100%; text-align: center; margin-top: 10px; display: block; }
    .btn-zoom:hover { background: #1a7ae0; }

    /* Sections */
    .section { padding: 60px 20px; text-align: center; }
    .steps { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-top: 30px; }
    .step { background: white; padding: 30px; border-radius: 20px; width: 250px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    
    /* Top Events */
    .top-events-section { background: white; padding: 60px 20px; margin-top: 40px; }
    .top-event-card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 20px; margin-bottom: 15px; max-width: 1000px; margin-left: auto; margin-right: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .rank-badge { background: #f39c12; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    
    /* Resources Grid */
    .resource-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
    .resource-item { border: 1px solid #eee; border-radius: 12px; padding: 15px; text-align: center; background: white; }
    .res-icon { font-size: 40px; display: block; margin-bottom: 10px; }
    
    /* Modal-like View (Full Page Overlay for Details) */
    .details-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
    .details-content { background: white; width: 90%; max-width: 800px; max-height: 90vh; border-radius: 20px; overflow-y: auto; position: relative; padding: 30px; }
    .close-btn { position: absolute; top: 20px; right: 20px; font-size: 30px; text-decoration: none; color: #333; }
    
    .detail-row { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .detail-label { font-weight: bold; color: #7b2ff7; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 14px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    /* Reviews List */
    .review-item { border-bottom: 1px solid #eee; padding: 15px 0; }
    .stars { color: #f39c12; }

    footer { text-align: center; padding: 30px; background: #1a1a2e; color: #ccc; margin-top: auto; }
    
    /* Messages */
    .msg-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    .msg-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
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

<!-- MESSAGES FLASH -->
<?php if(isset($successMsg)): ?>
    <div class="container"><div class="msg-success"><?= htmlspecialchars($successMsg) ?></div></div>
<?php endif; ?>
<?php if(isset($errorMsg)): ?>
    <div class="container"><div class="msg-error"><?= htmlspecialchars($errorMsg) ?></div></div>
<?php endif; ?>

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
            <div class="card" style="text-align:center; color:#888;">Aucun événement prévu pour le moment.</div>
        <?php endif; ?>
    </div>

    <!-- TOP ÉVÉNEMENTS TERMINÉS -->
    <?php if(!empty($pastEvents)): ?>
    <div class="top-events-section">
        <h2 style="color:#333; font-size: 28px; margin-bottom: 30px; text-align:center;">🏆 Les Événements les Mieux Notés</h2>
        <?php foreach($pastEvents as $index => $pe): 
            $avg = round($pe['avg_note'], 1);
            $etoiles = "";
            for($i=1; $i<=5; $i++) { $etoiles .= ($i <= round($avg)) ? "⭐" : "☆"; }
        ?>
        <div class="top-event-card">
            <div class="rank-badge"><?= $index + 1 ?></div>
            <div style="flex:1; text-align:left;">
                <h3 style="margin:0 0 5px 0; color:#333;"><?= htmlspecialchars($pe['titre']) ?></h3>
                <p style="margin:0; color:#888; font-size:14px;">Terminé le <?= date('d/m/Y', strtotime($pe['date_fin'])) ?></p>
            </div>
            <div style="text-align:right;">
                <div class="stars"><?= $etoiles ?> (<?= $avg ?>/5)</div>
                <a href="index.php?view=event_details&id=<?= $pe['id_evenement'] ?>&show_reviews=1" class="btn btn-outline" style="font-size:12px; padding:5px 10px; margin-top:5px;">
                    👁️ Voir les <?= $pe['count_reviews'] ?> avis
                </a>
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
            <div class="step"><h3>3. Collabore</h3><p>Travaille et apprends</p></div>
        </div>
    </div>

<!-- VUE RESSOURCES -->
<?php elseif($viewMode === 'resources'): ?>
    <div class="hero" style="padding-bottom: 60px;">
        <h1>🛠️ Nos Ressources Disponibles</h1>
    </div>
    <div class="container">
        <form method="GET" style="background:white; padding:20px; border-radius:10px; display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">
            <input type="hidden" name="view" value="resources">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding:10px; border:1px solid #ddd; border-radius:5px;">
            <select name="type" style="padding:10px; border:1px solid #ddd; border-radius:5px;">
                <option value="">Tous les types</option>
                <option value="Matériel" <?= (isset($_GET['type']) && $_GET['type'] === 'Matériel') ? 'selected' : '' ?>>Matériel</option>
                <option value="Salle" <?= (isset($_GET['type']) && $_GET['type'] === 'Salle') ? 'selected' : '' ?>>Salle</option>
                <option value="Équipement" <?= (isset($_GET['type']) && $_GET['type'] === 'Équipement') ? 'selected' : '' ?>>Équipement</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="index.php?view=resources" class="btn btn-outline">Reset</a>
        </form>

        <div class="resource-grid">
            <?php if(!empty($resourcesList)): ?>
                <?php foreach($resourcesList as $res): 
                    $icon = '📦';
                    if($res['type'] === 'Salle') $icon = '🏫';
                    if($res['type'] === 'Matériel') $icon = '💻';
                ?>
                <div class="resource-item">
                    <span class="res-icon"><?= $icon ?></span>
                    <strong><?= htmlspecialchars($res['nom']) ?></strong>
                    <p style="color:#888; font-size:12px;"><?= htmlspecialchars($res['type']) ?></p>
                    <p style="color:#28a745; font-weight:bold;">Dispo: <?= (int)$res['quantite_disponible'] ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center;">Aucune ressource trouvée.</p>
            <?php endif; ?>
        </div>
    </div>

<!-- VUE DÉTAILS ÉVÉNEMENT (MODAL FULL PAGE) -->
<?php elseif($viewMode === 'event_details' && $eventDetails): ?>
    <div class="details-overlay">
        <div class="details-content">
            <a href="index.php" class="close-modal close-btn">&times;</a>
            
            <h2 style="color:#7b2ff7; margin-top:0;"><?= htmlspecialchars($eventDetails['titre']) ?></h2>
            
            <div class="detail-grid">
                <div class="detail-row">
                    <span class="detail-label">📅 Date de début</span>
                    <span><?= $eventDetails['date_formatee'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">🏁 Date de fin</span>
                    <span><?= $eventDetails['date_fin_formatee'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">📍 Lieu</span>
                    <span><?= htmlspecialchars($eventDetails['lieu']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">👥 Capacité</span>
                    <span><?= (int)$eventDetails['capacite_max'] ?> personnes</span>
                </div>
            </div>

            <!-- BOUTON ZOOM OU INSCRIPTION -->
            <?php if ($eventDetails['lieu'] === 'En ligne' && !empty($eventDetails['lien_reunion'])): ?>
                <div style="background:#eef2ff; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; border:1px solid #7b2ff7;">
                    <p style="margin:0 0 10px 0; font-weight:bold; color:#7b2ff7;">🎥 Événement en Ligne</p>
                    <a href="<?= htmlspecialchars($eventDetails['lien_reunion']) ?>" target="_blank" class="btn btn-zoom">
                        🔗 Rejoindre la réunion (Zoom/Meet)
                    </a>
                </div>
            <?php elseif (!$eventDetails['is_past']): ?>
                <a href="?action=register&id=<?= $eventDetails['id_evenement'] ?>" class="btn btn-primary" style="width:100%; text-align:center; margin-bottom:20px;">
                     S'inscrire à cet événement
                </a>
            <?php endif; ?>

            <div class="detail-row" style="border:none;">
                <span class="detail-label">📝 Description complète</span>
                <p style="white-space: pre-line; line-height:1.6;"><?= nl2br(htmlspecialchars($eventDetails['description'])) ?></p>
            </div>

            <!-- SECTION NOTATION SI PASSÉ -->
            <?php if ($eventDetails['is_past']): ?>
                <hr style="margin:30px 0; border:0; border-top:1px solid #eee;">
                
                <?php if(isset($_GET['show_rate'])): ?>
                    <!-- Formulaire de notation -->
                    <div style="background:#f9f9f9; padding:20px; border-radius:10px;">
                        <h3>📝 Noter cet événement</h3>
                        <form method="POST">
                            <input type="hidden" name="action_event_feedback" value="1">
                            <input type="hidden" name="id_evenement" value="<?= $eventDetails['id_evenement'] ?>">
                            
                            <div style="margin-bottom:15px;">
                                <label>Votre note :</label><br>
                                <select name="note" style="font-size:20px; padding:5px; margin-top:5px;">
                                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                    <option value="4">⭐⭐⭐⭐ (Très bien)</option>
                                    <option value="3">⭐⭐⭐ (Correct)</option>
                                    <option value="2">⭐⭐ (Moyen)</option>
                                    <option value="1">⭐ (À améliorer)</option>
                                </select>
                            </div>
                            <div style="margin-bottom:15px;">
                                <label>Commentaire :</label><br>
                                <textarea name="commentaire" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="Votre avis..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                            <a href="index.php?view=event_details&id=<?= $idEvent ?>" class="btn btn-outline" style="margin-left:10px;">Annuler</a>
                        </form>
                    </div>
                <?php else: ?>
                    <button onclick="window.location.href='index.php?view=event_details&id=<?= $idEvent ?>&show_rate=1'" class="btn btn-rate">
                        📝 Noter cet événement
                    </button>
                <?php endif; ?>

                <!-- Affichage des avis existants -->
                <?php if(isset($_GET['show_reviews']) || !empty($reviews)): ?>
                    <div style="margin-top:30px;">
                        <h3>💬 Avis des participants</h3>
                        <?php if(empty($reviews)): ?>
                            <p style="color:#888;">Aucun avis pour le moment.</p>
                        <?php else: ?>
                            <?php foreach($reviews as $avis): 
                                $etoiles = '';
                                for($i=1; $i<=5; $i++) { $etoiles .= ($i <= $avis['note']) ? '⭐' : '☆'; }
                            ?>
                            <div class="review-item">
                                <div style="display:flex; justify-content:space-between;">
                                    <strong class="stars"><?= $etoiles ?></strong>
                                    <small style="color:#888;"><?= date('d/m/Y', strtotime($avis['date_feedback'])) ?></small>
                                </div>
                                <p style="margin:5px 0 0 0; color:#555;"><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
<?php endif; ?>

<!-- PIED DE PAGE COMMUN -->
<?php if($viewMode !== 'event_details'): ?>
<div class="section" style="background: white;">
    <h2>Compétences populaires</h2>
    <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap; margin-top:20px;">
        <span style="background:#e7f9ff; color:#007bff; padding:10px 20px; border-radius:50px; font-weight:bold;">Développement Web</span>
        <span style="background:#e7f9ff; color:#007bff; padding:10px 20px; border-radius:50px; font-weight:bold;">Design</span>
        <span style="background:#e7f9ff; color:#007bff; padding:10px 20px; border-radius:50px; font-weight:bold;">Montage Vidéo</span>
    </div>
</div>

<footer>
    <p>© 2026 SkillSwap - Tous droits réservés</p>
</footer>
<?php endif; ?>

</body>
</html>