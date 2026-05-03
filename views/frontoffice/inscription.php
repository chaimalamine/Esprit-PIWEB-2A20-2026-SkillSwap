<?php
if (!isset($success, $message, $ref)) {
    header('Location: ../../index.php?page=inscription');
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Inscription - SkillSwap</title>

<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{
font-family:'Segoe UI',sans-serif;
background:
radial-gradient(circle at top left, rgba(123,47,247,0.18), transparent 24%),
linear-gradient(180deg,#faf8ff 0%,#f3efff 100%);
}

.navbar{
display:flex;justify-content:space-between;align-items:center;
padding:18px 40px;background:rgba(255,255,255,0.9);
backdrop-filter:blur(10px);border-bottom:1px solid #ece7ff;
}

.navbar h2{color:#7b2ff7;}
.navbar a{
text-decoration:none;margin:0 10px;color:#5b21b6;font-weight:600;
}

.auth-container{
display:flex;justify-content:center;align-items:center;
min-height:90vh;padding:30px 20px;
}

.auth-card{
background:rgba(255,255,255,0.94);padding:40px;border-radius:28px;
width:430px;
box-shadow:0 20px 50px rgba(91,33,182,0.12);
border:1px solid #ece7ff;
}

.auth-card h2{
text-align:center;color:#7b2ff7;margin-bottom:22px;
}

input{
width:100%;
padding:13px 14px;
margin:8px 0;
border-radius:14px;
border:1px solid #ddd6fe;
background:#fcfbff;
}

button{
width:100%;
padding:13px;
background:linear-gradient(90deg,#7b2ff7,#a855f7);
border:none;
color:white;
border-radius:18px;
cursor:pointer;
font-weight:700;
box-shadow:0 12px 26px rgba(123,47,247,0.22);
}

.msg{
text-align:center;
margin-bottom:15px;
font-weight:bold;
padding:12px;
border-radius:14px;
background:#f5f3ff;
}

.msg.error{
background:#fef2f2;
color:#b91c1c;
border:1px solid #fecaca;
}

.msg.success{
background:#f0fdf4;
color:#166534;
border:1px solid #bbf7d0;
}

.help{
font-size:13px;
color:#6b7280;
margin:2px 0 10px;
}

.parrain{
color:#15803d;
text-align:center;
margin-bottom:15px;
padding:10px;
background:#f0fdf4;
border-radius:12px;
}

.credits-box{
background:linear-gradient(135deg,#ffffff,#f6f0ff);
border:1px solid #ddd6fe;
padding:16px;
border-radius:18px;
margin:15px 0;
}

.credits-box strong{
display:block;
font-size:24px;
color:#7b2ff7;
margin-top:5px;
}

.notifications-box{
background:linear-gradient(135deg,#ffffff,#f6f0ff);
border:1px solid #ddd6fe;
padding:16px;
border-radius:18px;
margin:15px 0;
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
</style>

</head>

<body>

<div class="navbar">
<h2>SkillSwap</h2>
<div>
<a href="index.php?page=frontoffice">Accueil</a>
<a href="index.php?page=frontoffice">Explorer</a>
<a href="index.php?page=backoffice">Proposer</a>
</div>
</div>

<div class="auth-container">

<div class="auth-card">

<h2>Creer un compte</h2>

<?php if (!empty($utilisateurActuel)): ?>
<div class="credits-box">
<div>Mon compteur de credits</div>
<strong><?= (int) ($utilisateurActuel['credits_gratuits'] ?? 0) ?></strong>
<input value="<?= htmlspecialchars($utilisateurActuel['lien_personnel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly>
</div>
<?php endif; ?>

<?php if (!empty($notifications)): ?>
<div class="notifications-box">
<div style="font-weight:700;color:#7b2ff7;margin-bottom:8px;">Notifications</div>
<?php foreach ($notifications as $notification): ?>
<div class="notification-item <?= htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8') ?>">
<?= htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($success): ?>

<div class="msg success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>

<?php else: ?>

<?php if ($message !== ''): ?>
<div class="msg error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if ($ref !== ''): ?>
<div class="parrain">
Parrain : <b><?= htmlspecialchars($ref, ENT_QUOTES, 'UTF-8') ?></b>
</div>
<?php endif; ?>

<form method="POST" action="index.php?page=inscription<?php if ($ref !== ''): ?>&ref=<?= urlencode($ref) ?><?php endif; ?><?php if (!empty($offreCode)): ?>&offer=<?= urlencode($offreCode) ?><?php endif; ?>">
<input type="hidden" name="action" value="register">

<label for="nom">Nom</label>
<input id="nom" type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<label for="prenom">Prenom</label>
<input id="prenom" type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Email</label>
<input id="email" type="text" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<label for="mot_de_passe">Mot de passe</label>
<input id="mot_de_passe" type="password" name="mot_de_passe">
<div class="help">Le mot de passe doit contenir au moins 8 caracteres avec des lettres et des chiffres.</div>

<input type="hidden" name="parraine_par" value="<?= htmlspecialchars($ref, ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="offer_code" value="<?= htmlspecialchars($offreCode ?? '', ENT_QUOTES, 'UTF-8') ?>">

<button type="submit">S'inscrire</button>

</form>

<?php endif; ?>

</div>

</div>

</body>
</html>
