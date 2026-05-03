<?php
if (!isset($offres)) {
    header('Location: ../../index.php?page=backoffice');
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Dashboard</title>

<style>
body {
margin:0;font-family:'Segoe UI';display:flex;color:#1f2937;
background:linear-gradient(180deg,#f6f4ff 0%,#eef2f7 100%);
}

.sidebar {
width:240px;
background:linear-gradient(180deg,#6a11cb,#2575fc);
color:white;height:100vh;padding:24px;
box-shadow:18px 0 40px rgba(37,117,252,0.12);
}

.sidebar a {
display:block;color:white;margin:15px 0;padding:12px 14px;border-radius:12px;text-decoration:none;font-weight:600;
}

.sidebar a:hover {background:rgba(255,255,255,0.2);}

.main {flex:1;padding:24px;}

.topbar {
background:rgba(255,255,255,0.94);padding:18px 20px;border-radius:18px;
display:flex;justify-content:space-between;box-shadow:0 14px 30px rgba(31,41,55,0.08);
border:1px solid #ebe6ff;
}

.card {
background:linear-gradient(135deg,#ffb4c1,#ffd9a8);
padding:22px;border-radius:20px;margin:14px 0;
box-shadow:0 16px 32px rgba(31,41,55,0.08);
}

.box {
background:rgba(255,255,255,0.95);padding:18px;margin:14px 0;border-radius:18px;
box-shadow:0 16px 32px rgba(31,41,55,0.08);border:1px solid #ebe6ff;
}

input,textarea{
width:100%;padding:11px 13px;margin:6px 0;border-radius:12px;
border:1px solid #ddd6fe;background:#fcfbff;
}

label{
display:block;margin-top:8px;color:#5b21b6;font-weight:600;
}

button{
    padding:10px 14px;
    border:none;
    border-radius:12px;
    cursor:pointer;
    margin:5px 3px 0 0;
    font-weight:600;
    background:linear-gradient(90deg,#8b5cf6,#6d28d9);
    color:white;
    transition:0.3s;
    box-shadow:0 10px 22px rgba(109,40,217,0.18);
}

button:hover{
    transform:translateY(-1px);
}

.hidden{display:none;}

.actions-bar{
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap;
    margin:10px 0 16px;
}

.btn-link{
    display:inline-block;
    padding:10px 14px;
    border-radius:12px;
    background:#ef4444;
    color:white;
    text-decoration:none;
    font-weight:600;
}

.btn-link:hover{
    background:#dc2626;
}
</style>

<script>
function showParrainage(){
document.getElementById("dashboard").style.display="none";
document.getElementById("parrainage").style.display="block";
}

function showDashboard(){
document.getElementById("dashboard").style.display="block";
document.getElementById("parrainage").style.display="none";
}

window.onload = function(){
showDashboard();
}

function detail(t,d){
alert("Titre: "+t+"\n\nDescription: "+d);
}
</script>

</head>

<body>

<div class="sidebar">
<h2>SkillSwap</h2>

<a href="#" onclick="showDashboard()">Dashboard</a>
<a href="#" onclick="showParrainage()">Offre Parrainage</a>
<a href="index.php?page=frontoffice">Frontoffice</a>
</div>

<div class="main">

<div class="topbar">
<h3>Bienvenue</h3>
</div>

<div id="dashboard">
<div class="card">10 credits</div>
<div class="card">5 echanges</div>
<div class="card">Note : 4.8/5</div>
</div>

<div id="parrainage" class="hidden">

<h2>Gestion Offre Parrainage</h2>

<form method="GET" action="index.php">
<input type="hidden" name="page" value="backoffice">
<label for="search">Recherche</label>
<input id="search" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
<button type="submit">Rechercher</button>
</form>

<div class="actions-bar">
<a class="btn-link" href="index.php?page=backoffice&amp;action=export_pdf<?= ($search ?? '') !== '' ? '&amp;search=' . urlencode($search) : '' ?>">Exporter PDF</a>
</div>

<form method="POST" action="index.php?page=backoffice">
<input type="hidden" name="action" value="store">
<label for="titre">Titre</label>
<input id="titre" type="text" name="titre">
<label for="description">Description</label>
<textarea id="description" name="description"></textarea>
<label for="recompense">Nombre de credits gratuits</label>
<input id="recompense" type="text" name="recompense">
<label for="invitations_requises">Nombre des invitations</label>
<input id="invitations_requises" type="text" name="invitations_requises">
<label for="date_fin">Date de fin du timer</label>
<input id="date_fin" type="text" name="date_fin" value="<?= date('Y-m-d') ?>">
<button type="submit">Creer</button>
</form>

<?php if (!empty($utilisateursEnAttente)): ?>
<h3>Inscriptions en attente</h3>
<?php foreach ($utilisateursEnAttente as $user): ?>
<div class="box">
<b><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom'], ENT_QUOTES, 'UTF-8') ?></b><br>
<small>Email : <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></small><br>
<small>Parrain : <?= htmlspecialchars($user['parraine_par'] !== '' ? $user['parraine_par'] : 'Aucun', ENT_QUOTES, 'UTF-8') ?></small><br>
<small>Offre : <?= htmlspecialchars($user['offre_code'] !== '' ? $user['offre_code'] : 'Aucune', ENT_QUOTES, 'UTF-8') ?></small><br>
<small>Date inscription : <?= htmlspecialchars($user['date_inscription'], ENT_QUOTES, 'UTF-8') ?></small><br>
<form method="GET" action="index.php">
<input type="hidden" name="page" value="backoffice">
<input type="hidden" name="action" value="validate_user">
<input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
<button type="submit">Valider</button>
</form>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php foreach ($offres as $o): ?>

<div class="box">

<b><?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?></b><br>
<?= nl2br(htmlspecialchars($o->getDescription(), ENT_QUOTES, 'UTF-8')) ?><br><br>
<small>Credits gratuits : <?= (int) $o->getRecompenseParrain() ?></small><br>
<small>Invitations requises : <?= (int) $o->getInvitationsRequises() ?></small><br>
<small>Inscriptions liees au parrainage : <?= (int) $o->getTotalInscriptionsLiees() ?></small><br>
<small>Parrainages effectifs : <?= (int) $o->getTotalParrainagesLies() ?></small><br>
<small>
Timer :
<?= htmlspecialchars($o->getDateFin() !== '' && $o->getDateFin() !== '0000-00-00' ? $o->getDateFin() : 'Non defini', ENT_QUOTES, 'UTF-8') ?>
</small><br><br>

<button onclick="detail('<?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?>','<?= htmlspecialchars($o->getDescription(), ENT_QUOTES, 'UTF-8') ?>')">
Voir detail
</button>

<form method="POST" action="index.php?page=backoffice">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="<?= $o->getIdOffre() ?>">
<input name="titre" value="<?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?>">
<input name="description" value="<?= htmlspecialchars($o->getDescription(), ENT_QUOTES, 'UTF-8') ?>">
<button type="submit">Modifier</button>
</form>

<form method="GET" action="index.php">
<input type="hidden" name="page" value="backoffice">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="delete" value="<?= $o->getIdOffre() ?>">
<button type="submit">Supprimer</button>
</form>

</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>
