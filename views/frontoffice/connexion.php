<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SkillSwap</title> 
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar h2 { color: #7b2ff7; text-decoration: none; }
        .navbar a { margin: 0 15px; text-decoration: none; color: #333; font-weight: 500; }
        .navbar a:hover { color: #7b2ff7; }
        .navbar button { background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; }
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 70px); padding: 20px; }
        .auth-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); width: 100%; max-width: 450px; }
        .auth-card h2 { text-align: center; color: #7b2ff7; margin-bottom: 30px; font-size: 26px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #444; }
        .form-group input { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 15px; background: #fafafa; }
        .form-group input:focus { outline: none; border-color: #7b2ff7; background: white; box-shadow: 0 0 0 4px rgba(123, 47, 247, 0.1); }
        .form-group input.valid { border-color: #10b981 !important; }
        .form-group input.invalid { border-color: #ef4444 !important; }
        .validation-message { font-size: 12px; margin-top: 5px; min-height: 18px; }
        .validation-message.valid { color: #10b981; }
        .validation-message.invalid { color: #ef4444; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
        .success-message { background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
        .btn-submit { width: 100%; padding: 15px; background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; font-size: 16px; font-weight: bold; border-radius: 20px; cursor: pointer; margin-top: 10px; opacity: 0.5; transition: 0.3s; }
        .btn-submit.enabled { opacity: 1; }
        .btn-submit.enabled:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123, 47, 247, 0.4); }
        .auth-footer { text-align: center; margin-top: 25px; font-size: 14px; color: #666; }
        .auth-footer a { color: #7b2ff7; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="frontdesign.php"><h2>SkillSwap</h2></a>
        <div>
            <a href="frontdesign.php">Accueil</a>
            <a href="#">Explorer</a>
            <a href="#">Proposer</a>
            <button onclick="window.location.href='connexion.php'">Commencer</button>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Se connecter</h2>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['erreur'])): ?>
                <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
            <?php endif; ?>

            <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=login" method="POST" id="loginForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <div class="validation-message" id="emailMessage"></div>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" id="password" placeholder="••••••••">
                    <div class="validation-message" id="passwordMessage"></div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>Se connecter</button>
            </form>

            <div class="auth-footer">
                Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
// ÉTAPE 1 : RÉCUPÉRER LES ÉLÉMENTS HTML PAR LEUR ID
// Ces variables vont pointer vers les balises de la page
// ============================================================

// Récupérer le champ input de l'email (là où on écrit)
const emailInput = document.getElementById('email');

// Récupérer le champ input du mot de passe
const passwordInput = document.getElementById('password');

// Récupérer la <div> où on va afficher le message de validation de l'email
const emailMessage = document.getElementById('emailMessage');

// Récupérer la <div> où on va afficher le message de validation du mot de passe
const passwordMessage = document.getElementById('passwordMessage');

// Récupérer le bouton "Se connecter" (pour l'activer/désactiver)
const submitBtn = document.getElementById('submitBtn');

// ============================================================
// ÉTAPE 2 : VARIABLES D'ÉTAT (vrai/faux)
// Elles mémorisent si chaque champ est valide ou non
// ============================================================

// Au début, l'email n'est pas valide (champ vide)
let emailValid = false;

// Au début, le mot de passe n'est pas valide (champ vide)
let passwordValid = false;

// ============================================================
// ÉTAPE 3 : FONCTION DE VALIDATION DE L'EMAIL
// Appelée à chaque fois que l'utilisateur tape dans le champ email
// ============================================================
function validateEmail() {
    // Récupérer la valeur du champ email et enlever les espaces au début/fin
    const email = emailInput.value.trim();
    
    // Définir le format accepté pour un email (xxx@xxx.xxx)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // --- CAS 1 : Le champ est vide ---
    if (email === '') {
        // Enlever les classes de couleur (valid=vert, invalid=rouge)
        emailInput.classList.remove('valid', 'invalid');
        // Effacer le message de validation
        emailMessage.textContent = '';
        // Enlever les classes de couleur du message
        emailMessage.classList.remove('valid', 'invalid');
        // Marquer l'email comme NON valide
        emailValid = false;
    }
    // --- CAS 2 : L'email ne correspond pas au format attendu ---
    else if (!emailRegex.test(email)) {
        // Ajouter la classe rouge sur le champ
        emailInput.classList.add('invalid');
        // Enlever la classe verte sur le champ
        emailInput.classList.remove('valid');
        // Afficher le message d'erreur en rouge
        emailMessage.textContent = '❌ Format d\'email invalide';
        // Ajouter la classe rouge sur le message
        emailMessage.classList.add('invalid');
        // Enlever la classe verte sur le message
        emailMessage.classList.remove('valid');
        // Marquer l'email comme NON valide
        emailValid = false;
    }
    // --- CAS 3 : L'email est correct ---
    else {
        // Ajouter la classe verte sur le champ
        emailInput.classList.add('valid');
        // Enlever la classe rouge sur le champ
        emailInput.classList.remove('invalid');
        // Afficher le message de succès en vert
        emailMessage.textContent = '✅ Email valide';
        // Ajouter la classe verte sur le message
        emailMessage.classList.add('valid');
        // Enlever la classe rouge sur le message
        emailMessage.classList.remove('invalid');
        // Marquer l'email comme valide
        emailValid = true;
    }
    // Appeler la fonction qui active/désactive le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 4 : FONCTION DE VALIDATION DU MOT DE PASSE
// Appelée à chaque fois que l'utilisateur tape dans le champ mot de passe
// ============================================================
function validatePassword() {
    // Récupérer la valeur du champ mot de passe
    const password = passwordInput.value;
    
    // --- CAS 1 : Le champ est vide ---
    if (password === '') {
        // Enlever les classes de couleur
        passwordInput.classList.remove('valid', 'invalid');
        // Effacer le message
        passwordMessage.textContent = '';
        // Enlever les classes de couleur du message
        passwordMessage.classList.remove('valid', 'invalid');
        // Marquer comme NON valide
        passwordValid = false;
    }
    // --- CAS 2 : Le champ est rempli (n'importe quel contenu) ---
    else {
        // Ajouter la classe verte sur le champ
        passwordInput.classList.add('valid');
        // Enlever la classe rouge sur le champ
        passwordInput.classList.remove('invalid');
        // Afficher le message de succès
        passwordMessage.textContent = '✅ Mot de passe saisi';
        // Ajouter la classe verte sur le message
        passwordMessage.classList.add('valid');
        // Enlever la classe rouge sur le message
        passwordMessage.classList.remove('invalid');
        // Marquer comme valide
        passwordValid = true;
    }
    // Appeler la fonction qui active/désactive le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 5 : FONCTION QUI ACTIVE/DÉSACTIVE LE BOUTON
// Si TOUS les champs sont valides → bouton activé
// Sinon → bouton désactivé (grisé)
// ============================================================
function updateSubmitButton() {
    // Si l'email EST valide ET le mot de passe EST valide
    if (emailValid && passwordValid) {
        // Activer le bouton (on peut cliquer dessus)
        submitBtn.disabled = false;
        // Ajouter la classe CSS qui rend le bouton violet
        submitBtn.classList.add('enabled');
    } else {
        // Désactiver le bouton (on ne peut pas cliquer)
        submitBtn.disabled = true;
        // Enlever la classe CSS, le bouton redevient gris
        submitBtn.classList.remove('enabled');
    }
}

// ============================================================
// ÉTAPE 6 : CONNECTER LES FONCTIONS AUX ÉVÉNEMENTS
// ============================================================

// Quand l'utilisateur tape dans le champ email → appeler validateEmail()
emailInput.addEventListener('input', validateEmail);

// Quand l'utilisateur tape dans le champ mot de passe → appeler validatePassword()
passwordInput.addEventListener('input', validatePassword);

// ============================================================
// ÉTAPE 7 : VALIDATION AU CHARGEMENT DE LA PAGE
// Si des valeurs sont déjà remplies (ex: après une erreur), les valider
// ============================================================
window.addEventListener('load', function() {
    // Si le champ email n'est pas vide
    if (emailInput.value.trim() !== '') {
        validateEmail();    // Valider l'email
    }
    // Si le champ mot de passe n'est pas vide
    if (passwordInput.value !== '') {
        validatePassword(); // Valider le mot de passe
    }
});
    </script>
</body>
</html>