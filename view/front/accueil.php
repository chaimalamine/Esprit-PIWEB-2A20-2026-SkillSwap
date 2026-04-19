<?php
include_once "../../controller/groupeC.php";

$gc = new groupeC();
$groupes = $gc->listGroupes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - Accueil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; }
        .navbar h2 { color: #7b2ff7; }
        .navbar a { margin: 0 10px; text-decoration: none; color: #333; }
        .navbar button { background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; padding: 8px 15px; border-radius: 20px; cursor: pointer; }
        .hero { text-align: center; padding: 80px 20px; background: linear-gradient(135deg, #7b2ff7, #c084fc); color: white; }
        .hero h1 { font-size: 40px; }
        .hero span { color: #fde68a; }
        .hero button { margin: 10px; padding: 10px 20px; border: none; border-radius: 20px; cursor: pointer; }
        .btn1 { background: white; color: #7b2ff7; }
        .btn2 { background: #9333ea; color: white; }
        .section { padding: 50px 20px; text-align: center; }
        .steps { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .step { background: white; padding: 20px; border-radius: 15px; width: 220px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .skills { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .skill { background: #e7f9ff; padding: 15px 25px; border-radius: 15px; }
        .groups-container { background: #f9fafb; padding: 50px 20px; }
        .groups-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto; }
        .group-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: left; }
        .group-card h3 { color: #7b2ff7; margin-bottom: 10px; }
        .group-card p { color: #666; margin-bottom: 15px; }
        .group-meta { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #888; }
        .badge { background: #e9d5ff; color: #7b2ff7; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        .group-btn { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; width: 100%; }
        .group-btn-outline { background: transparent; border: 1px solid #7b2ff7; color: #7b2ff7; padding: 8px 15px; border-radius: 20px; cursor: pointer; margin-right: 10px; }
        .testimonials { background: linear-gradient(135deg, #7b2ff7, #c084fc); color: white; padding: 50px 20px; text-align: center; }
        .testimonials p { background: white; color: #333; padding: 15px; border-radius: 10px; display: inline-block; margin: 10px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; border-radius: 20px; padding: 30px; width: 450px; max-width: 90%; }
        .modal-content input, .modal-content textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 10px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
        footer { text-align: center; padding: 20px; background: #111; color: white; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>SkillSwap</h2>
    <div>
        <a href="accueil.php">Accueil</a>
        <a href="groupes.php">Groupes</a>
        <a href="#">Explorer</a>
        <a href="#">Proposer</a>
        <button onclick="alert('Bienvenue sur SkillSwap !')">Commencer</button>
    </div>
</div>

<div class="hero">
    <h1>Échange tes <span>compétences</span><br>sans dépenser d'argent</h1>
    <button class="btn1" onclick="window.location.href='groupes.php'">Trouver un échange</button>
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

<div class="groups-container">
    <div class="section" style="padding: 0;">
        <h2>Rejoins des communautés</h2>
        <div class="groups-grid">
            <?php if (!empty($groupes)): ?>
                <?php foreach($groupes as $groupe): ?>
                <div class="group-card">
                    <h3><?= htmlspecialchars($groupe['nom']) ?></h3>
                    <p><?= htmlspecialchars(substr($groupe['description'], 0, 100)) ?>...</p>
                    <div class="group-meta">
                        <span>0 membres</span>
                        <span class="badge">Groupe</span>
                    </div>
                    <button class="group-btn-outline" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Rejoindre</button>
                    <button class="group-btn" onclick="window.location.href='groupedetail.php?id=<?= $groupe['idgroup'] ?>'">Voir</button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun groupe disponible.</p>
            <?php endif; ?>
        </div>
        <br>
        <button onclick="openCreateGroupModal()" style="background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer;">➕ Créer mon groupe</button>
    </div>
</div>

<div class="testimonials">
    <h2>Ils ont échangé leurs compétences</h2>
    <p>Grâce à SkillSwap, j'ai créé mon logo gratuitement</p>
    <p>J'ai appris le design en échangeant mes services</p>
</div>

<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <h3>Créer un nouveau groupe</h3>
        <form id="createGroupForm" method="POST" action="../back/groupes/create.php">
            <input type="text" name="nom" id="groupNom" placeholder="Nom du groupe" required>
            <textarea name="description" id="groupDescription" rows="4" placeholder="Description du groupe"></textarea>
            <div class="modal-actions">
                <button type="button" onclick="closeCreateGroupModal()" style="background: #ccc;">Annuler</button>
                <button type="submit" style="background: linear-gradient(90deg, #7b2ff7, #a855f7);">Créer</button>
            </div>
        </form>
    </div>
</div>

<footer>© 2026 SkillSwap</footer>

<script>
    function openCreateGroupModal() {
        document.getElementById('createGroupModal').style.display = 'flex';
    }
    function closeCreateGroupModal() {
        document.getElementById('createGroupModal').style.display = 'none';
    }
    window.onclick = function(event) {
        const modal = document.getElementById('createGroupModal');
        if (event.target === modal) closeCreateGroupModal();
    }
    
    document.getElementById('createGroupForm')?.addEventListener('submit', function(e) {
        var nom = document.getElementById('groupNom').value.trim();
        var desc = document.getElementById('groupDescription').value.trim();
        if (nom.length < 3) {
            alert("Le nom doit contenir au moins 3 caractères");
            e.preventDefault();
            return false;
        }
        if (desc.length < 10) {
            alert("La description doit contenir au moins 10 caractères");
            e.preventDefault();
            return false;
        }
        return true;
    });
</script>

</body>
</html>