<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillSwap</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; }

/* ── Navbar ── */
.navbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0 48px; height: 68px; background: white;
    box-shadow: 0 1px 0 #ede9ff;
    position: sticky; top: 0; z-index: 100;
}
.navbar-brand { display: flex; align-items: center; gap: 9px; text-decoration: none; }
.navbar-brand .dot { width: 11px; height: 11px; background: #7b2ff7; border-radius: 50%; }
.navbar-brand span { color: #7b2ff7; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
.navbar-links { display: flex; align-items: center; gap: 2px; }
.navbar-links a {
    padding: 8px 18px; text-decoration: none; color: #555;
    font-size: 14px; font-weight: 500; border-radius: 22px;
    transition: background 0.15s, color 0.15s;
}
.navbar-links a:hover { background: #f5f3ff; color: #7b2ff7; }
.navbar-links .btn-start {
    background: linear-gradient(135deg, #7b2ff7, #a855f7);
    color: white !important; margin-left: 8px; font-weight: 600;
    box-shadow: 0 2px 8px rgba(123,47,247,0.25);
}
.navbar-links .btn-start:hover { opacity: 0.9; background: linear-gradient(135deg, #7b2ff7, #a855f7); }

/* ── Hero ── */
.hero {
    text-align: center; padding: 96px 20px 80px;
    background: linear-gradient(140deg, #5b12d4 0%, #7b2ff7 40%, #a855f7 75%, #c084fc 100%);
    color: white;
}
.hero-badge {
    display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
    border-radius: 20px; padding: 5px 18px; font-size: 13px; font-weight: 500; margin-bottom: 24px;
}
.hero h1 { font-size: 46px; font-weight: 800; line-height: 1.15; margin-bottom: 18px; letter-spacing: -0.5px; }
.hero h1 span { color: #fde68a; }
.hero p { font-size: 17px; opacity: 0.85; max-width: 520px; margin: 0 auto 36px; line-height: 1.6; }
.hero-btns { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
.hero-btns a { text-decoration: none; }
.btn-hero-white {
    background: white; color: #7b2ff7; padding: 13px 30px;
    border-radius: 26px; font-size: 15px; font-weight: 700;
    border: none; cursor: pointer; transition: all 0.2s;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.btn-hero-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
.btn-hero-outline {
    background: rgba(255,255,255,0.12); color: white; padding: 13px 30px;
    border-radius: 26px; font-size: 15px; font-weight: 600;
    border: 1.5px solid rgba(255,255,255,0.4); cursor: pointer; transition: all 0.2s;
}
.btn-hero-outline:hover { background: rgba(255,255,255,0.22); transform: translateY(-2px); }

/* ── Sections ── */
.section { padding: 72px 48px; text-align: center; }
.section h2 { color: #1a0533; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
.section .sub { color: #888; font-size: 15px; margin-bottom: 44px; }

/* Steps */
.steps { display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; }
.step {
    background: white; padding: 32px 26px; border-radius: 20px; width: 220px;
    box-shadow: 0 4px 20px rgba(123,47,247,0.07); border: 1px solid #f0eaff;
    transition: transform 0.2s, box-shadow 0.2s;
}
.step:hover { transform: translateY(-4px); box-shadow: 0 10px 32px rgba(123,47,247,0.13); }
.step .step-num {
    width: 44px; height: 44px; background: linear-gradient(135deg, #7b2ff7, #a855f7);
    border-radius: 50%; color: white; font-size: 17px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.step h3 { color: #1a0533; font-size: 16px; margin-bottom: 8px; }
.step p  { color: #888; font-size: 13px; line-height: 1.5; }

/* Skills */
.skills { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
.skill {
    background: white; color: #7b2ff7; padding: 12px 24px; border-radius: 14px;
    font-size: 14px; font-weight: 600; border: 1.5px solid #e0d9f7;
    transition: all 0.2s;
}
.skill:hover { background: #7b2ff7; color: white; transform: translateY(-2px); }

/* Testimonials */
.testimonials {
    background: linear-gradient(135deg, #5b12d4, #7b2ff7 50%, #a855f7);
    padding: 72px 48px; text-align: center;
}
.testimonials h2 { color: white; font-size: 28px; font-weight: 700; margin-bottom: 8px; }
.testimonials .sub { color: rgba(255,255,255,0.7); font-size: 15px; margin-bottom: 40px; }
.testi-grid { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
.testi-card {
    background: white; color: #333; padding: 24px 26px; border-radius: 18px;
    max-width: 280px; text-align: left; font-size: 14px; line-height: 1.6;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}
.testi-card .author { color: #7b2ff7; font-weight: 600; font-size: 13px; margin-top: 12px; }

/* CTA Section */
.cta-section {
    padding: 72px 48px; text-align: center;
    background: white;
}
.cta-section h2 { color: #1a0533; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
.cta-section p  { color: #888; font-size: 15px; margin-bottom: 28px; }

/* Footer */
footer {
    text-align: center; padding: 28px; background: #0f0520; color: #888; font-size: 13px;
}
footer span { color: #7b2ff7; }
</style>
<script src="/SkillSwap/js/skillswap.js" defer></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <a href="/SkillSwap/index.php" class="navbar-brand">
        <div class="dot"></div>
        <span>SkillSwap</span>
    </a>
    <div class="navbar-links">
        <a href="/SkillSwap/index.php">Accueil</a>
        <a href="/SkillSwap/view/cours/liste_cours.php">Explorer</a>
        <a href="/SkillSwap/view/cours/ajout_cours.php">Proposer</a>
        <a href="/SkillSwap/view/cours/liste_cours.php" class="btn-start">Commencer</a>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-badge">✨ La plateforme d'échange de compétences</div>
    <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
    <p>Rejoins une communauté qui apprend et partage ensemble. Donne ce que tu sais, reçois ce dont tu as besoin.</p>
    <div class="hero-btns">
        <a href="/SkillSwap/view/cours/liste_cours.php"><button class="btn-hero-white">Trouver un échange</button></a>
        <a href="/SkillSwap/view/cours/ajout_cours.php"><button class="btn-hero-outline">Proposer un service</button></a>
    </div>
</section>

<!-- Steps -->
<section class="section">
    <h2>Comment ça marche ?</h2>
    <p class="sub">En 3 étapes simples, commencez à échanger vos compétences</p>
    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <h3>Ajoute</h3>
            <p>Propose tes compétences et décris ce que tu peux offrir à la communauté.</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h3>Trouve</h3>
            <p>Explore les offres disponibles et trouve un échange qui te correspond.</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h3>Collabore</h3>
            <p>Travaillez ensemble, apprenez et progressez mutuellement.</p>
        </div>
    </div>
</section>

<!-- Skills -->
<section class="section" style="background:white;padding-top:56px;padding-bottom:56px;">
    <h2>Compétences populaires</h2>
    <p class="sub">Des centaines de compétences à échanger</p>
    <div class="skills">
        <div class="skill">💻 Développement</div>
        <div class="skill">🎨 Design</div>
        <div class="skill">🎬 Montage Vidéo</div>
        <div class="skill">📣 Marketing</div>
        <div class="skill">📸 Photographie</div>
        <div class="skill">🎵 Musique</div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials">
    <h2>Ils ont échangé leurs compétences</h2>
    <p class="sub">Des milliers d'échanges réussis sur SkillSwap</p>
    <div class="testi-grid">
        <div class="testi-card">
            "Grâce à SkillSwap, j'ai pu créer mon logo gratuitement en échangeant mes cours de guitare."
            <div class="author">— Yasmine B.</div>
        </div>
        <div class="testi-card">
            "J'ai appris le design en quelques semaines en échangeant mes compétences en développement."
            <div class="author">— Karim A.</div>
        </div>
        <div class="testi-card">
            "Une plateforme incroyable pour apprendre sans dépenser. Je recommande à tous !"
            <div class="author">— Sofia M.</div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2>Prêt à commencer l'échange ?</h2>
    <p>Rejoins des centaines de membres qui apprennent et partagent ensemble.</p>
    <div class="hero-btns">
        <a href="/SkillSwap/view/cours/liste_cours.php"><button class="btn-hero-white" style="background:linear-gradient(135deg,#7b2ff7,#a855f7);color:white;box-shadow:0 4px 16px rgba(123,47,247,0.35)">Explorer les cours</button></a>
        <a href="/SkillSwap/view/demande/ajout_demande.php"><button class="btn-hero-outline" style="border-color:#e0d9f7;color:#7b2ff7;background:white">Soumettre une demande</button></a>
    </div>
</section>

<footer>
    © 2026 <span>SkillSwap</span> — Plateforme d'échange de compétences
</footer>

</body>
</html>
