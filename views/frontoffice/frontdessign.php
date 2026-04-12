<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap</title>
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

    .hero button {
        margin: 10px;
        padding: 10px 20px;
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
        transition: transform 0.3s;
    }
    .step:hover { transform: translateY(-5px); }
    .step h3 { color: #7b2ff7; }

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
    .testimonials {
        background: linear-gradient(135deg, #7b2ff7, #c084fc);
        color: white;
        padding: 50px 20px;
    }

    .testimonials p {
        background: white;
        color: #333;
        padding: 15px;
        border-radius: 10px;
        display: inline-block;
        margin: 10px;
    }

    /* FOOTER */
    footer {
        text-align: center;
        padding: 30px;
        background: #1a1a2e;
        color: #ccc;
    }
</style>
</head>

<body>
<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="frontdessign.php">Accueil</a>
        <a href="#">Explorer</a>
        <a href="#">Proposer</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="../backoffice/profil.php" id="com">Mon Profil</a>
        <?php else: ?>
            <a href="../backoffice/inscription.php" id="com">Commencer</a>
        <?php endif; ?>
    </div>
</div>

<div class="hero">
    <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
    <button class="btn1">Trouver un échange</button>
    <button class="btn2">Proposer un service</button>
</div>

<div class="section">
    <h2>Comment ça marche ?</h2>
    <div class="steps">
        <div class="step"><h3>1. Ajoute</h3><p>Ajoute tes compétences</p></div>
        <div class="step"><h3>2. Trouve</h3><p>Cherche un échange</p></div>
        <div class="step"><h3>3. Collabore</h3><p>Travaille et apprends</p></div>
    </div>
</div>

<div class="section">
    <h2>Compétences populaires</h2>
    <div class="skills">
        <div class="skill">Développement</div>
        <div class="skill">Design</div>
        <div class="skill">Montage Vidéo</div>
        <div class="skill">Marketing</div>
    </div>
</div>

<div class="testimonials">
    <h2>Ils ont échangé leurs compétences</h2>
    <p>Grâce à SkillSwap, j'ai créé mon logo gratuitement</p>
    <p>J'ai appris le design en échangeant mes services</p>
</div>

<footer>© 2026 SkillSwap</footer>
</body>
</html>