<?php
if (!isset($offres)) {
    header('Location: ../../index.php?page=frontoffice');
    exit;
}

$couleursRoues = ['#7b2ff7', '#ec4899', '#22c55e', '#f59e0b', '#06b6d4', '#ef4444', '#8b5cf6', '#14b8a6'];
$offresRoue = [];
foreach ($offres as $index => $offre) {
    $offresRoue[] = [
        'titre' => $offre->getTitre(),
        'participants' => $offre->getParticipants(),
        'couleur' => $couleursRoues[$index % count($couleursRoues)],
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Parrainage</title>

<style>
body{
font-family:'Segoe UI';margin:0;color:#1f2937;
background:
radial-gradient(circle at top left, rgba(123,47,247,0.16), transparent 24%),
linear-gradient(180deg,#f8f7ff 0%,#f3f0ff 100%);
}

.navbar{
display:flex;justify-content:space-between;align-items:center;
padding:18px 40px;background:rgba(255,255,255,0.9);
backdrop-filter:blur(10px);border-bottom:1px solid #ece7ff;
position:sticky;top:0;z-index:10;
}
.navbar h2{color:#7b2ff7;margin:0}
.navbar a{
text-decoration:none;color:#5b21b6;font-weight:600;
padding:10px 14px;border-radius:999px;background:#f4efff;
}

.page-wrapper{display:flex;gap:20px;padding:24px}

.sidebar{
width:250px;background:rgba(255,255,255,0.86);padding:22px;border-radius:22px;
box-shadow:0 16px 40px rgba(91,33,182,0.08);border:1px solid #ece7ff;
height:fit-content;
}

.sidebar h3{margin-top:0}

.main{flex:1}

.card{
background:rgba(255,255,255,0.92);padding:22px;margin:14px 0;border-radius:22px;
box-shadow:0 16px 36px rgba(31,41,55,0.08);border:1px solid #ede9fe;
}

.stats-wheel-box{
background:rgba(255,255,255,0.92);
padding:22px;
margin:18px 0;
border-radius:22px;
box-shadow:0 16px 36px rgba(31,41,55,0.08);
border:1px solid #ede9fe;
display:flex;
gap:20px;
align-items:center;
flex-wrap:wrap;
}

.credits-box{
background:linear-gradient(135deg,#ffffff,#f6f0ff);
padding:18px;
margin:18px 0;
border-radius:22px;
box-shadow:0 16px 36px rgba(31,41,55,0.08);
border:1px solid #ede9fe;
}

.credits-box strong{
display:block;
font-size:24px;
color:#7b2ff7;
margin-top:5px;
}

.stats-wheel{
width:220px;
height:220px;
flex:0 0 220px;
position:relative;
display:flex;
align-items:center;
justify-content:center;
}

.stats-wheel canvas{
width:220px;
height:220px;
}

.stats-wheel-center{
position:absolute;
inset:0;
display:flex;
align-items:center;
justify-content:center;
flex-direction:column;
z-index:1;
text-align:center;
font-size:14px;
color:#4b5563;
pointer-events:none;
}

.stats-wheel-center strong{
font-size:24px;
color:#7b2ff7;
}

.stats-legend{
flex:1;
min-width:260px;
}

.stats-legend-item{
display:flex;
align-items:center;
justify-content:space-between;
gap:10px;
padding:10px 0;
border-bottom:1px solid #f0e9ff;
}

.stats-legend-item:last-child{
border-bottom:none;
}

.stats-percent{
font-size:12px;
color:#6b7280;
margin-left:6px;
}

.stats-legend-left{
display:flex;
align-items:center;
gap:10px;
}

.stats-color{
width:14px;
height:14px;
border-radius:50%;
display:inline-block;
}

.participants{
margin:12px 0 14px;
}

.timer-info{
margin:12px 0 14px;
font-size:0.95rem;
color:#4b5563;
background:#faf7ff;
padding:12px 14px;
border-radius:14px;
}

.timer-info div{
margin-bottom:4px;
}

.participants-label{
display:flex;
justify-content:space-between;
font-size:0.95rem;
margin-bottom:6px;
color:#4b5563;
}

.participants-bar{
height:12px;
background:#e9d5ff;
border-radius:999px;
overflow:hidden;
}

.participants-fill{
height:100%;
background:linear-gradient(90deg,#7b2ff7,#ec4899);
border-radius:999px;
transition:width 0.3s ease;
}

button{
background:linear-gradient(90deg,#7b2ff7,#a855f7);color:white;border:none;
padding:10px 14px;margin:5px;border-radius:12px;cursor:pointer;font-weight:600;
box-shadow:0 10px 22px rgba(123,47,247,0.2);
}

input{
padding:11px 14px;width:220px;border-radius:12px;border:1px solid #ddd6fe;
background:#fff;
}
</style>

<script>
const wheelOffers = <?= json_encode($offresRoue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

function search(){
    let k = document.getElementById("search").value.toLowerCase();
    let cards = document.getElementsByClassName("offer-card");

    for(let i=0;i<cards.length;i++){
        cards[i].style.display =
        cards[i].innerText.toLowerCase().includes(k) ? "block":"none";
    }

    renderWheel();
}

function participer(code){
let link="http://localhost/parrainage-project/index.php?page=inscription&ref="+code;

let popup=`
<div id="popup" style="
position:fixed;top:0;left:0;width:100%;height:100%;
background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;
">

<div style="background:white;padding:20px;border-radius:10px;width:350px;text-align:center;">

<h3>Lien de parrainage</h3>

<input id="refLink" value="${link}" readonly style="width:100%;padding:8px;"><br><br>

<button onclick="copy()">Copier</button>
<button onclick="closePopup()">Fermer</button>

</div>
</div>
`;

document.body.insertAdjacentHTML("beforeend",popup);
}

function copy(){
let i=document.getElementById("refLink");
i.select();
document.execCommand("copy");
alert("Lien copie !");
}

function closePopup(){
document.getElementById("popup").remove();
}

function detail(t,d){
alert("Titre: "+t+"\n\nDescription: "+d);
}

function getVisibleWheelData() {
    let cards = Array.from(document.getElementsByClassName("offer-card"));
    let visibleTitles = cards
        .filter(card => card.style.display !== "none")
        .map(card => card.dataset.titre);

    return wheelOffers.filter(item =>
        visibleTitles.includes(item.titre) && Number(item.participants) > 0
    );
}

function drawDonut(progress = 1) {
    let canvas = document.getElementById("statsWheelCanvas");
    let ctx = canvas.getContext("2d");
    let data = getVisibleWheelData();
    let total = data.reduce((sum, item) => sum + Number(item.participants), 0);
    let size = 220;
    let center = size / 2;
    let radius = 92;
    let lineWidth = 34;

    ctx.clearRect(0, 0, size, size);
    ctx.lineWidth = lineWidth;
    ctx.lineCap = "butt";

    ctx.beginPath();
    ctx.strokeStyle = "#e9d5ff";
    ctx.arc(center, center, radius, 0, Math.PI * 2);
    ctx.stroke();

    document.getElementById("wheelTotal").textContent = total;

    if (total <= 0) {
        document.getElementById("wheelLegend").innerHTML = "<h3>Roulette des offres</h3><p>Aucune participation pour le moment.</p>";
        return;
    }

    let startAngle = -Math.PI / 2;
    for (let i = 0; i < data.length; i++) {
        let item = data[i];
        let fullSlice = (Number(item.participants) / total) * Math.PI * 2;
        let visibleSlice = fullSlice * progress;

        ctx.beginPath();
        ctx.strokeStyle = item.couleur;
        ctx.arc(center, center, radius, startAngle, startAngle + visibleSlice);
        ctx.stroke();

        startAngle += fullSlice;
    }

    let legend = "<h3>Roulette des offres</h3>";
    for (let i = 0; i < data.length; i++) {
        let item = data[i];
        let percent = ((Number(item.participants) / total) * 100).toFixed(1);
        legend += `
        <div class="stats-legend-item">
            <div class="stats-legend-left">
                <span class="stats-color" style="background:${item.couleur};"></span>
                <span>${item.titre}<span class="stats-percent">${percent}%</span></span>
            </div>
            <strong>${item.participants}</strong>
        </div>`;
    }
    document.getElementById("wheelLegend").innerHTML = legend;
}

function renderWheel() {
    let start = null;

    function animate(timestamp) {
        if (!start) {
            start = timestamp;
        }

        let progress = Math.min((timestamp - start) / 700, 1);
        drawDonut(progress);

        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
}

window.onload = function () {
    document.getElementById("search").addEventListener("input", search);
    renderWheel();
}
</script>

</head>

<body>

<div class="navbar">
<h2>SkillSwap</h2>
<a href="index.php?page=backoffice">Backoffice</a>
</div>

<div class="page-wrapper">

<div class="sidebar">
<h3>Menu</h3>
<p>Parrainage</p>
</div>

<div class="main">

<h2>Offres de Parrainage</h2>

<?php if (!empty($utilisateurActuel)): ?>
<div class="credits-box">
<div>Mon compteur de credits</div>
<strong><?= (int) ($utilisateurActuel['credits_gratuits'] ?? 0) ?></strong>
<input value="<?= htmlspecialchars($utilisateurActuel['lien_personnel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly>
</div>
<?php endif; ?>

<div class="stats-wheel-box">
<div class="stats-wheel">
<canvas id="statsWheelCanvas" width="220" height="220"></canvas>
<div class="stats-wheel-center">
<span>Total</span>
<strong id="wheelTotal">0</strong>
<span>participants</span>
</div>
</div>

<div class="stats-legend" id="wheelLegend">
<h3>Roulette des offres</h3>
</div>
</div>

<input id="search" placeholder="Rechercher">
<button onclick="search()">Chercher</button>

<?php
$maxParticipants = max(1, (int) ($statistiques['maxParticipants'] ?? 0));
foreach ($offres as $o):
    $pourcentageParticipation = min(100, (int) round(($o->getParticipants() / $maxParticipants) * 100));
    $codeOffre = $o->getCodeParrainUnique() !== '' ? $o->getCodeParrainUnique() : ('OFFRE' . $o->getIdOffre());
    $dateFinAffichee = 'Non definie';
    $tempsRestant = 'Aucun timer';

    if ($o->getDateFin() !== '' && $o->getDateFin() !== '0000-00-00') {
        $dateFinAffichee = date('d/m/Y', strtotime($o->getDateFin()));
        $today = new DateTime(date('Y-m-d'));
        $dateFin = new DateTime($o->getDateFin());

        if ($dateFin < $today) {
            $tempsRestant = 'Offre expiree';
        } else {
            $interval = $today->diff($dateFin);
            $jours = (int) $interval->format('%a');
            $tempsRestant = $jours === 0 ? 'Expire aujourd\'hui' : $jours . ' jour(s)';
        }
    }
?>

<div class="card offer-card"
     data-titre="<?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?>"
     data-participants="<?= (int) $o->getParticipants() ?>">

<b><?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?></b><br>
<?= nl2br(htmlspecialchars($o->getDescription(), ENT_QUOTES, 'UTF-8')) ?><br><br>

<div class="timer-info">
<div><b>Credits gratuits :</b> <?= (int) $o->getRecompenseParrain() ?></div>
<div><b>Invitations requises :</b> <?= (int) $o->getInvitationsRequises() ?></div>
<div><b>Date de fin :</b> <?= htmlspecialchars($dateFinAffichee, ENT_QUOTES, 'UTF-8') ?></div>
<div><b>Temps restant :</b> <?= htmlspecialchars($tempsRestant, ENT_QUOTES, 'UTF-8') ?></div>
</div>

<div class="participants">
<div class="participants-label">
<span>Participants</span>
<span><?= (int) $o->getParticipants() ?></span>
</div>
<div class="participants-bar">
<div class="participants-fill" style="width: <?= $pourcentageParticipation ?>%;"></div>
</div>
</div>

<button onclick="detail('<?= htmlspecialchars($o->getTitre(), ENT_QUOTES, 'UTF-8') ?>','<?= htmlspecialchars($o->getDescription(), ENT_QUOTES, 'UTF-8') ?>')">
Voir detail
</button>

<button onclick="participer('<?= htmlspecialchars($codeOffre, ENT_QUOTES, 'UTF-8') ?>')">
Participer
</button>

</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>
