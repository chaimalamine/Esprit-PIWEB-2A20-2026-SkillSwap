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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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

.notifications-box{
background:rgba(255,255,255,0.92);
padding:18px;
margin:18px 0;
border-radius:22px;
box-shadow:0 16px 36px rgba(31,41,55,0.08);
border:1px solid #ede9fe;
}

.notification-item{
padding:12px 14px;
border-radius:14px;
margin:10px 0;
font-weight:600;
}

.notification-item.info{
background:#eff6ff;
color:#1d4ed8;
border:1px solid #bfdbfe;
}

.notification-item.success{
background:#f0fdf4;
color:#166534;
border:1px solid #bbf7d0;
}

.notification-item.warning{
background:#fff7ed;
color:#9a3412;
border:1px solid #fdba74;
}

.msg{
text-align:center;
margin:18px 0;
font-weight:bold;
padding:12px;
border-radius:14px;
background:#f0fdf4;
color:#166534;
border:1px solid #bbf7d0;
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

.progress-box{
margin:12px 0 14px;
padding:12px 14px;
border-radius:14px;
background:#f5f3ff;
border:1px solid #ddd6fe;
}

.progress-box strong{
color:#5b21b6;
}

.status-badge{
display:inline-block;
margin-top:8px;
padding:6px 10px;
border-radius:999px;
font-size:13px;
font-weight:700;
}

.status-badge.en-cours{
background:#ede9fe;
color:#6d28d9;
}

.status-badge.objectif-atteint{
background:#dcfce7;
color:#166534;
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
const appBaseUrl = <?= json_encode(APP_BASE_URL, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const currentOriginBase = window.location.origin + "/parrainage-project";

function search(){
    let k = document.getElementById("search").value.toLowerCase();
    let cards = document.getElementsByClassName("offer-card");

    for(let i=0;i<cards.length;i++){
        cards[i].style.display =
        cards[i].innerText.toLowerCase().includes(k) ? "block":"none";
    }

}

function participer(code){
var xhr = new XMLHttpRequest();
xhr.open("GET", currentOriginBase + "/index.php?page=frontoffice&action=participer&code=" + encodeURIComponent(code), true);
xhr.onreadystatechange = function() {
    var data;

    if (xhr.readyState !== 4) {
        return;
    }

    try {
        data = JSON.parse(xhr.responseText);
    } catch (e) {
        alert("Reponse invalide du serveur.");
        return;
    }

    if (xhr.status < 200 || xhr.status >= 300) {
        alert(data.message || "Participation impossible.");
        return;
    }

        let link = data.link;
        let qrLink = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" + encodeURIComponent(link);
        let qrLinkFallback = "https://quickchart.io/qr?size=220&text=" + encodeURIComponent(link);

        let popup=`
        <div id="popup" style="
        position:fixed;top:0;left:0;width:100%;height:100%;
        background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;
        ">

        <div style="background:white;padding:20px;border-radius:10px;width:360px;text-align:center;">

        <h3>Lien de parrainage</h3>
        <p style="color:#4b5563;font-size:14px;margin-bottom:14px;">${data.message}</p>

        <input id="refLink" value="${link}" readonly style="width:100%;padding:8px;"><br><br>

        <img id="popupQrImage" src="${qrLink}" alt="QR Code parrainage" style="width:220px;height:220px;border-radius:12px;border:1px solid #e5e7eb;object-fit:cover;"><br><br>

        <div id="qrFallback" style="display:none;margin:12px 0;padding:10px 12px;background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;color:#991b1b;font-size:13px;">
        Le service du QR code n'a pas charge. Le lien ci-dessus reste utilisable.
        </div>

        <a href="${link}" target="_blank" style="display:inline-block;margin-bottom:10px;color:#7b2ff7;text-decoration:none;font-weight:600;">
        Ouvrir la page d'inscription
        </a><br>

        <button onclick="copy()">Copier</button>
        <button onclick="closePopup()">Fermer</button>

        </div>
        </div>
        `;

        document.body.insertAdjacentHTML("beforeend",popup);
        prepareQrFallback(qrLinkFallback);
};
xhr.onerror = function() {
    alert("Le serveur ne repond pas pour la participation.");
};
xhr.send(null);
}

function prepareQrFallback(qrLinkFallback){
var qrImage = document.getElementById("popupQrImage");
var qrFallback = document.getElementById("qrFallback");

if (!qrImage) {
    return;
}

qrImage.onerror = function() {
    if (qrImage.getAttribute("data-fallback-used") === "1") {
        qrImage.style.display = "none";
        if (qrFallback) {
            qrFallback.style.display = "block";
        }
        return;
    }

    qrImage.setAttribute("data-fallback-used", "1");
    qrImage.src = qrLinkFallback;
};
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

window.onload = function () {
    document.getElementById("search").onkeyup = search;
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

<?php if (!empty($flashSuccess)): ?>
<div class="msg"><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($notifications)): ?>
<div class="notifications-box">
<h3>Notifications</h3>
<?php foreach ($notifications as $notification): ?>
<div class="notification-item <?= htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8') ?>">
<?= htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<input id="search" name="search">
<button onclick="search()">Chercher</button>

<?php
$maxParticipants = max(1, (int) ($statistiques['maxParticipants'] ?? 0));
foreach ($offres as $o):
    $pourcentageParticipation = min(100, (int) round(($o->getParticipants() / $maxParticipants) * 100));
    $codeOffre = $o->getCodeParrainUnique() !== '' ? $o->getCodeParrainUnique() : ('OFFRE' . $o->getIdOffre());
    $progression = $progressionsOffres[$codeOffre] ?? null;
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

<?php if ($progression !== null): ?>
<div class="progress-box">
<div><strong>Mes invitations pour cette offre :</strong> <?= (int) $progression['invitations'] ?> / <?= (int) $progression['invitations_requises'] ?></div>
<div><strong>Credits a gagner :</strong> <?= (int) $progression['credits_a_gagner'] ?></div>
<div>
<strong>Statut :</strong>
<span class="status-badge <?= $progression['objectif_atteint'] ? 'objectif-atteint' : 'en-cours' ?>">
<?= htmlspecialchars($progression['statut'], ENT_QUOTES, 'UTF-8') ?>
</span>
</div>
</div>
<?php endif; ?>

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
