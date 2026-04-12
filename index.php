<?php
require_once 'model/Event.php';
session_start();
require_once 'controller/EventController.php';

$controller = new EventController();

// Gestion des inscri  
if(isset($_GET['action']) && $_GET['action'] == 'register') {
    echo "<script>alert('Inscription réussie !');</script>";
}

// Récupérer liste événements depuis la Base de donnes

$events = $controller->listeEvents();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap - Échange de compétences</title>
<style>
  
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f5f3ff;
        color: #333;
    }

    /* NAVBAR */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 40px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar h2 {
        color: #7b2ff7;
        margin: 0;
    }

    .navbar a {
        margin: 0 15px;
        text-decoration: none;
        color: #555;
        font-weight: 500;
        transition: color 0.3s;
    }

    .navbar a:hover { color: #7b2ff7; }

    .navbar button {
        background: linear-gradient(90deg, #7b2ff7, #a855f7);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        transition: transform 0.2s;
    }
    .navbar button:hover { transform: scale(1.05); }

    /* HERO SECTION */
    .hero {
        text-align: center;
        padding: 80px 20px 120px;
        background: linear-gradient(135deg, #7b2ff7, #c084fc);
        color: white;
        position: relative;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .hero span { color: #fde68a; }
    .hero p { font-size: 18px; opacity: 0.9; }

    /*SECTION ÉVÉNEMENTS*/
    .events-container {
        max-width: 1000px;
        margin: -60px auto 50px; 
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }

    .section-title {
        text-align: left;      
        color: white;
        font-size: 28px;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .event-card {
        background: white;
        padding: 25px;
        margin-bottom: 20px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .event-info h3 {
        color: #7b2ff7;
        margin: 0 0 8px 0;
        font-size: 22px;
    }

    .event-info p {
        color: #666;
        margin: 5px 0;
    }

    .event-meta {
        color: #888;
        font-size: 14px;
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }

    .btn-register {
        background: #7b2ff7;
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: bold;
        transition: background 0.3s;
    }
    .btn-register:hover { background: #5a1db8; }

    /* STEPS SECTION */
    .section {
        padding: 60px 20px;
        text-align: center;
    }

    .steps {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .step {
        background: white;
        padding: 30px;
        border-radius: 20px;
        width: 250px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .step h3 { color: #7b2ff7; }

    
    .skills {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .skill {
        background: #e7f9ff;
        color: #007bff;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: bold;
    }

    
    footer {
        text-align: center;
        padding: 30px;
        background: #1a1a2e;
        color: #ccc;
    }
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="index.php">Accueil</a>
        <a href="#">Explorer</a>
        <a href="dashboard.php">Espace Organisateur</a>
        <button>Commencer</button>
    </div>
</div>

<!-- Hero -->
<div class="hero">
    <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
    <p>Rejoins la communauté et apprends de nouvelles choses !</p>
</div>

<!-- Section Événements -->
<div class="events-container">
    <h2 class="section-title">--Événements à venir--</h2>

    <?php 
    // ✅ CORRECTION : Utilisation de $e['nom_colonne'] au lieu de $e->getMethod()
    // PDO retourne des tableaux associatifs par défaut
    if (!empty($events)) {
        foreach ($events as $e) {
            // Formatage de la date pour l'affichage
            $dateAffichee = !empty($e['date_debut']) ? date('d/m/Y', strtotime($e['date_debut'])) : 'Date non définie';
            
            echo '
            <div class="event-card">
                <div class="event-info">
                    <h3>' . htmlspecialchars($e['titre']) . '</h3>
                    <p>' . htmlspecialchars($e['description']) . '</p>
                    <span class="event-meta">📍 ' . htmlspecialchars($e['lieu']) . ' | 🕒 ' . $dateAffichee . '</span>
                </div>
                <a href="?action=register&id=' . $e['id_evenement'] . '" class="btn-register">S\'inscrire</a>
            </div>';
        }
    } else {
        echo '<div class="event-card" style="justify-content:center; color:#888;">Aucun événement prévu pour le moment.</div>';
    }
    ?>
</div>

<!-- comment ca marche -->
<div class="section">
    <h2>Comment ça marche ?</h2>
    <div class="steps">
        <div class="step">
            <h3>1. Ajoute</h3>
            <p>Ajoute tes compétences</p>
        </div>
        <div class="step">
            <h3>2. Trouve</h3>
            <p>Cherche un échange</p>
        </div>
        <div class="step">
            <h3>3. Collabore</h3>
            <p>Travaille et apprends</p>
        </div>
    </div>
</div>

<!-- competences -->
<div class="section" style="background: white;">
    <h2>Compétences populaires</h2>
    <div class="skills">
        <div class="skill">Développement Web</div>
        <div class="skill">Design Graphique</div>
        <div class="skill">Montage Vidéo</div>
        <div class="skill">Marketing Digital</div>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>© 2026 SkillSwap - Tous droits réservés</p>
</footer>

</body>
</html>