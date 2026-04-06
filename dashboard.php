<?php
// 1. TRÈS IMPORTANT : Charger la classe Event AVANT la session
// PHP a besoin de connaître la classe pour reconstruire les objets de la session.
require_once 'model/Event.php';

// 2. Démarrer la session
session_start();

// 3. Inclure le contrôleur
require_once 'controller/EventController.php';

$controller = new EventController();

// Gestion de l'ajout d'événement (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $controller->addEvent();
    header("Location: dashboard.php");
    exit();
}

// Gestion de la suppression (GET)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $controller->deleteEvent($_GET['id']);
    header("Location: dashboard.php");
    exit();
}

// Récupérer la liste
$events = $controller->listeEvents();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap Dashboard - Événements</title>
<style>
/* --- VOTRE CSS BACK OFFICE --- */
body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: #eef2f7; }
.sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; }
.sidebar h2 { text-align: center; margin-bottom: 30px; }
.sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; transition: 0.3s; }
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
.main { flex: 1; padding: 20px; }
.topbar { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.topbar h3 { margin: 0; color: #333; }

/* --- STYLE GESTION ÉVÉNEMENTS --- */
.card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
.form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
.btn-submit { background: #2575fc; border: none; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; }
.btn-submit:hover { background: #6a11cb; }

table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f9fa; color: #555; }
.btn-delete { background: #ff4b4b; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 12px; }
.btn-edit { background: #ffc107; color: black; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 12px; margin-right: 5px;}
</style>
</head>

<body>

<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="#">🛠 Mes Compétences</a>
    <a href="#">📢 Offres</a>
    <a href="index.php">🏠 Retour au Site</a>
</div>

<div class="main">

    <div class="topbar">
        <h3>Gestion des Événements</h3>
        <div style="display:flex; gap:10px; align-items:center;">
            <span>Bienvenue, Organisateur</span>
            <div style="width:40px; height:40px; background:#ddd; border-radius:50%;"></div>
        </div>
    </div>

    <!-- Formulaire de Création -->
    <div class="card">
        <h3 style="margin-top:0; color:#7b2ff7;">➕ Créer un nouvel événement</h3>
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Titre de l'événement</label>
                    <input type="text" name="title" placeholder="Ex: Atelier PHP" required>
                </div>
                <div class="form-group">
                    <label>Lieu</label>
                    <input type="text" name="location" placeholder="Ex: Salle A, Paris" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Détails de l'événement..." required></textarea>
            </div>
            <div class="form-group">
                <label>Date et Heure</label>
                <input type="datetime-local" name="date" required>
            </div>
            <button type="submit" name="add_event" class="btn-submit">Publier l'événement</button>
        </form>
    </div>

    <!-- Liste des Événements -->
    <div class="card">
        <h3 style="margin-top:0; color:#7b2ff7;">📋 Mes Événements</h3>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Lieu</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Boucle foreach pour afficher les objets Event
                if (!empty($events)) {
                    foreach ($events as $e) {
                        echo '
                        <tr>
                            <td><strong>' . htmlspecialchars($e->getTitle()) . '</strong><br><small style="color:#888;">' . htmlspecialchars($e->getDescription()) . '</small></td>
                            <td>' . htmlspecialchars($e->getLocation()) . '</td>
                            <td>' . $e->getDate() . '</td>
                            <td>
                                <a href="?action=delete&id=' . $e->getId() . '" class="btn-delete" onclick="return confirm(\'Supprimer ?\');">Supprimer</a>
                            </td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" style="text-align:center; color:#888;">Aucun événement créé.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>