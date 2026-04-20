<?php 
session_start();

// Récupérer les stats de compétences
$nbCompetences = 0;
$totalHeures = 0;

if(isset($_SESSION['user_id'])) {
    include '../../controllers/CompetenceC.php';
    $competenceC = new CompetenceC();
    $nbCompetences = $competenceC->countCompetencesByUser($_SESSION['user_id']);
    $totalHeures = $competenceC->getTotalHeuresByUser($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f5f3ff;
        color: #333;
        padding: 20px;
    }

    /* ========== LAYOUT PRINCIPAL ========== */
    .page-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ========== SIDE BAR PROFIL (GAUCHE) ========== */
    .profile-sidebar {
        width: 280px;
        background: white;
        padding: 25px 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-radius: 20px;
        flex-shrink: 0;
    }

    .profile-sidebar .avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #7b2ff7, #a855f7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 32px;
        font-weight: bold;
    }

    .profile-sidebar h3 {
        text-align: center;
        color: #333;
        margin-bottom: 5px;
        font-size: 18px;
    }

    .profile-sidebar .email {
        text-align: center;
        color: #888;
        font-size: 12px;
        margin-bottom: 10px;
        word-break: break-all;
    }

    .profile-sidebar .badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #cd7f32;
        color: white;
        margin: 10px 0;
        text-align: center;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
    }

    .profile-sidebar .score {
        text-align: center;
        color: #fbbf24;
        font-weight: 600;
        margin: 10px 0;
    }

    .profile-sidebar .separator {
        height: 1px;
        background: #e0e0e0;
        margin: 20px 0;
    }

    .profile-sidebar .stats-title {
        font-size: 14px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
    }

    .profile-sidebar .info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 13px;
    }

    .profile-sidebar .info-label {
        color: #888;
    }

    .profile-sidebar .info-value {
        font-weight: 600;
        color: #7b2ff7;
    }

    .profile-sidebar .btn-sidebar {
        display: block;
        padding: 12px;
        text-align: center;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        margin: 10px 0;
        transition: transform 0.2s;
    }

    .profile-sidebar .btn-sidebar:hover {
        transform: scale(1.02);
    }

    .profile-sidebar .btn-profil {
        background: linear-gradient(90deg, #7b2ff7, #a855f7);
        color: white;
    }

    .profile-sidebar .btn-dashboard {
        background: white;
        color: #7b2ff7;
        border: 2px solid #7b2ff7;
    }

    .profile-sidebar .btn-logout {
        background: #fee2e2;
        color: #b91c1c;
    }

    .profile-sidebar .login-message {
        text-align: center;
        padding: 20px 0;
        color: #888;
    }

    .profile-sidebar .btn-login {
        display: block;
        padding: 12px;
        text-align: center;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        background: linear-gradient(90deg, #7b2ff7, #a855f7);
        color: white;
        margin-top: 20px;
    }

    .profile-sidebar .link-register {
        display: block;
        text-align: center;
        margin-top: 10px;
        color: #7b2ff7;
        text-decoration: none;
        font-size: 13px;
    }

    /* ========== CONTENU PRINCIPAL (DROITE) ========== */
    .main-content {
        flex: 1;
        min-width: 0;
    }

    /* NAVBAR - PREND TOUTE LA LARGEUR */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 40px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
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

    .navbar button,
    .navbar #com {
        background: linear-gradient(90deg, #7b2ff7, #a855f7);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        transition: transform 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .navbar button:hover,
    .navbar #com:hover { transform: scale(1.05); }

    /* HERO SECTION */
    .hero {
        text-align: center;
        padding: 40px 20px 80px;
        background: linear-gradient(135deg, #7b2ff7, #c084fc);
        color: white;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .hero span { color: #fde68a; }
    .hero p { font-size: 18px; opacity: 0.9; }

    .hero button {
        margin: 10px;
        padding: 15px 30px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        transition: transform 0.2s;
    }
    .hero button:hover { transform: scale(1.05); }

    .btn1 { background: white; color: #7b2ff7; }
    .btn2 { background: #9333ea; color: white; }

    /* STEPS SECTION */
    .section {
        padding: 40px 20px;
        text-align: center;
        background: white;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .section h2 {
        font-size: 32px;
        margin-bottom: 20px;
        color: #333;
    }

    .steps {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .step {
        background: #f8f9fb;
        padding: 30px;
        border-radius: 20px;
        width: 250px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    .step:hover { transform: translateY(-5px); }
    .step h3 { color: #7b2ff7; }

    /* ========== SECTIONS PLEINE LARGEUR ========== */
    .full-width-section {
        padding: 40px 20px;
        text-align: center;
        background: white;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .full-width-section h2 {
        font-size: 32px;
        margin-bottom: 20px;
        color: #333;
    }

    /* SKILLS */
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
        transition: transform 0.2s;
    }
    .skill:hover { transform: scale(1.05); }

    /* TESTIMONIALS */
    .testimonials-full {
        background: linear-gradient(135deg, #7b2ff7, #c084fc);
        color: white;
        padding: 50px 20px;
        text-align: center;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .testimonials-full h2 {
        font-size: 32px;
        margin-bottom: 30px;
    }

    .testimonials-full p {
        background: white;
        color: #333;
        padding: 15px 25px;
        border-radius: 10px;
        display: inline-block;
        margin: 10px;
    }
    .user-bio {
    text-align: center;
    color: #666;
    font-size: 12px;
    margin: 8px 0 12px 0;
    font-style: italic;
    padding: 0 10px;
    line-height: 1.4;
}

    /* FOOTER */
    footer {
        text-align: center;
        padding: 30px;
        background: #1a1a2e;
        color: #ccc;
        border-radius: 20px;
    }
</style>
</head>

<body>

<!-- NAVBAR - TOUTE LARGEUR -->
<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="frontdesign.php">Accueil</a>
        <a href="#">Explorer</a>
        <a href="#">Proposer</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="../backoffice/profil.php" id="com">Mon Profil</a>
        <?php else: ?>
           <a href="../frontoffice/inscription.php" id="com">Commencer</a>
        <?php endif; ?>
    </div>
</div>

<!-- LAYOUT SIDE BAR + HERO + STEPS -->
<div class="page-wrapper">
   <!-- SIDE BAR PROFIL (GAUCHE) -->
    <div class="profile-sidebar">
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="avatar">
                <?php echo strtoupper(substr($_SESSION['user_prenom'] ?? 'U', 0, 1)); ?>
            </div>
            <h3><?php echo $_SESSION['user_prenom'] ?? ''; ?> <?php echo $_SESSION['user_nom'] ?? ''; ?></h3>
            <div class="email"><?php echo $_SESSION['user_email'] ?? ''; ?></div>
            
            <!-- BIO AJOUTÉE ICI -->
            <?php if(!empty($_SESSION['user_bio'])): ?>
                <div class="user-bio">
                    "<?php echo $_SESSION['user_bio']; ?>"
                </div>
            <?php endif; ?>
            
            <div class="badge">🏅 <?php echo $_SESSION['user_badge'] ?? 'Debutant'; ?></div>
            
            <div class="score">
                ⭐ <?php echo $_SESSION['user_score'] ?? '0'; ?> points
            </div>
            
            <div class="separator"></div>
            
            <div class="stats-title">📊 STATISTIQUES</div>
            
            <div class="info-item">
                <span class="info-label">Compétences</span>
                <span class="info-value"><?php echo $nbCompetences; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Heures échangées</span>
                <span class="info-value"><?php echo $totalHeures; ?>h</span>
            </div>
            <div class="info-item">
                <span class="info-label">Statut</span>
                <span class="info-value"><?php echo $_SESSION['user_statut'] ?? 'Actif'; ?></span>
            </div>
            
            <div class="separator"></div>
            
            <a href="../backoffice/profil.php" class="btn-sidebar btn-profil">👤 Mon Profil</a>
            
            <?php if(isset($_SESSION['user_statut']) && $_SESSION['user_statut'] == 'Admin'): ?>
                <a href="../backoffice/design.php" class="btn-sidebar btn-dashboard">⚙️ Dashboard</a>
            <?php endif; ?>
            
            <a href="../controllers/UserC.php?action=logout" class="btn-sidebar btn-logout">🚪 Déconnexion</a>
            
        <?php else: ?>
            <div class="login-message">
                <div class="avatar" style="background: #ccc;">👤</div>
                <h3>Non connecté</h3>
                <p style="margin: 15px 0; color: #888;">Connectez-vous pour voir votre profil</p>
                <a href="../frontoffice/connexion.php" class="btn-login">Se connecter</a>
                <a href="../frontffice/inscription.php" class="link-register">Créer un compte</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- CONTENU PRINCIPAL (HERO + STEPS) -->
    <div class="main-content">
        <div class="hero">
            <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
            <p>Rejoins la communauté et développe tes talents gratuitement</p>
            <button class="btn1">Trouver un échange</button>
            <button class="btn2">Proposer un service</button>
        </div>

        <div class="section">
            <h2>Comment ça marche ?</h2>
            <div class="steps">
                <a href="../backoffice/mes_competences.php" style="text-decoration: none; color: inherit;">
    <div class="step">
        <h3>1. Ajoute</h3>
        <p>Ajoute tes compétences</p>
    </div>
</a>
                <div class="step"><h3>2. Trouve</h3><p>Cherche un échange</p></div>
                
            </div>
        </div>
    </div>
</div>

<!-- COMPÉTENCES POPULAIRES - TOUTE LARGEUR -->
<div class="full-width-section">
    <h2>Compétences populaires</h2>
    <div class="skills">
        <div class="skill">Développement</div>
        <div class="skill">Design</div>
        <div class="skill">Montage Vidéo</div>
        <div class="skill">Marketing</div>
    </div>
</div>

<!-- TÉMOIGNAGES - TOUTE LARGEUR -->
<div class="testimonials-full">
    <h2>Ils ont échangé leurs compétences</h2>
    <p>Grâce à SkillSwap, j'ai créé mon logo gratuitement</p>
    <p>J'ai appris le design en échangeant mes services</p>
</div>

<!-- FOOTER - TOUTE LARGEUR -->
<footer>© 2026 SkillSwap - Tous droits réservés</footer>

</body>
</html>