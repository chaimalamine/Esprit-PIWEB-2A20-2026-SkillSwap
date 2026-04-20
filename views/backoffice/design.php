<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SkillSwap Dashboard</title>
<style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: #eef2f7; }
    .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
    .sidebar h2 { text-align: center; }
    .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
    .sidebar a:hover { background: rgba(255,255,255,0.2); }
   .main { 
    margin-left: 250px;  /* ← 220px + 30px d'espace */
    padding: 20px; 
    flex: 1; 
}
    .topbar { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
    .card { background: linear-gradient(135deg, #ff9a9e, #fad0c4); padding: 20px; border-radius: 15px; color: #333; font-weight: bold; }
    .card:nth-child(2) { background: linear-gradient(135deg, #a18cd1, #fbc2eb); }
    .card:nth-child(3) { background: linear-gradient(135deg, #fbc2eb, #a6c1ee); }
    .section { margin-top: 30px; }
    .task { background: white; padding: 15px; margin: 10px 0; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; }
    button { background: #2575fc; border: none; color: white; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
    button:hover { background: #6a11cb; }
</style>
</head>
<body>
<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="design.php">Dashboard</a>
    <a href="liste_users.php">Liste des utilisateurs</a>
    <a href="mes_competences.php">Mes Compétences</a>
    <a href="#">Offres</a>
    <a href="#">Messages</a>
    <a href="profil.php">Profil</a>
    <a href="../frontoffice/frontdessign.php" style="margin-top: 30px; background: rgba(255,255,255,0.1);">← Retour au site</a>
</div>
<div class="main">
    <div class="topbar">
        <h3>Bienvenue, <?php echo $_SESSION['user_prenom']; ?> <?php echo $_SESSION['user_nom']; ?></h3>
        <a href="http://localhost/projetwebfinal/controllers/logout.php" style="background: #ef4444; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px;">Déconnexion</a>
    </div>
    <div class="cards">
        <div class="card">10 crédits</div>
        <div class="card">5 échanges</div>
        <div class="card">Note : 4.8/5</div>
    </div>
    <div class="section">
        <h3>Missions recommandées</h3>
        <div class="task"><span>Créer un logo</span><button>Échanger</button></div>
        <div class="task"><span>Montage vidéo</span><button>Échanger</button></div>
        <div class="task"><span>Développement site web</span><button>Échanger</button></div>
    </div>
</div>
</body>
</html>