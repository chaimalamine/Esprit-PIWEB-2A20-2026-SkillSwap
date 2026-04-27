<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - SkillSwap</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar h2 { color: #7b2ff7; text-decoration: none; }
        .navbar a { margin: 0 15px; text-decoration: none; color: #333; font-weight: 500; }
        .navbar a:hover { color: #7b2ff7; }
        .navbar button { background: linear-gradient(90deg, #7b2ff7, #a855f7); border: none; color: white; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: 600; }
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 70px); padding: 20px; }
        .auth-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); width: 100%; max-width: 480px; }
        .auth-card h2 { text-align: center; color: #7b2ff7; margin-bottom: 30px; font-size: 26px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #444; }
        .form-group input, .form-group select { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 15px; background: #fafafa; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #7b2ff7; background: white; box-shadow: 0 0 0 4px rgba(123, 47, 247, 0.1); }
        .form-group input.valid, .form-group select.valid { border-color: #10b981 !important; }
        .form-group input.invalid, .form-group select.invalid { border-color: #ef4444 !important; }
        .validation-message { font-size: 12px; margin-top: 5px; min-height: 18px; }
        .validation-message.valid { color: #10b981; }
        .validation-message.invalid { color: #ef4444; }
        .error-message { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
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
            <button onclick="window.location.href='inscription.php'">Commencer</button>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Créer un compte</h2>
            
            <?php if(isset($_SESSION['erreur'])): ?>
                <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
            <?php endif; ?>

            <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=register" method="POST" id="registerForm">
                
                <!-- NOM -->
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" id="nom" placeholder="Ton nom de famille" value="<?php echo isset($_POST['nom']) ? $_POST['nom'] : ''; ?>">
                    <div class="validation-message" id="nomMessage"></div>
                </div>

                <!-- PRÉNOM -->
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" id="prenom" placeholder="Ton prénom" value="<?php echo isset($_POST['prenom']) ? $_POST['prenom'] : ''; ?>">
                    <div class="validation-message" id="prenomMessage"></div>
                </div>

                <!-- EMAIL -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    <div class="validation-message" id="emailMessage"></div>
                </div>

                <!-- MOT DE PASSE -->
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" id="password" placeholder="••••••••">
                    <div class="validation-message" id="passwordMessage"></div>
                </div>

                <!-- CONFIRMATION -->
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirm_mot_de_passe" id="confirmPassword" placeholder="••••••••">
                    <div class="validation-message" id="confirmMessage"></div>
                </div>

                <!-- RÔLE -->
                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role" id="role">
                        <option value="utilisateur">Utilisateur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>S'inscrire</button>
            </form>

            <div class="auth-footer">
                Déjà un compte ? <a href="connexion.php">Se connecter</a>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
// ÉTAPE 1 : RÉCUPÉRER TOUS LES ÉLÉMENTS HTML PAR LEUR ID
// Ces variables pointent vers les balises de la page
// ============================================================

// Champs du formulaire (là où l'utilisateur écrit)
const nomInput = document.getElementById('nom');                // Champ "Nom"
const prenomInput = document.getElementById('prenom');          // Champ "Prénom"
const emailInput = document.getElementById('email');            // Champ "Email"
const passwordInput = document.getElementById('password');      // Champ "Mot de passe"
const confirmInput = document.getElementById('confirmPassword'); // Champ "Confirmer"

// Messages de validation (affichés sous chaque champ)
const nomMessage = document.getElementById('nomMessage');           // Message pour le nom
const prenomMessage = document.getElementById('prenomMessage');     // Message pour le prénom
const emailMessage = document.getElementById('emailMessage');       // Message pour l'email
const passwordMessage = document.getElementById('passwordMessage'); // Message pour le mot de passe
const confirmMessage = document.getElementById('confirmMessage');   // Message pour la confirmation

// Bouton "S'inscrire"
const submitBtn = document.getElementById('submitBtn');

// ============================================================
// ÉTAPE 2 : VARIABLES D'ÉTAT (true = valide, false = invalide)
// Mémorisent l'état de chaque champ
// ============================================================
let nomValid = false;         // État du champ Nom
let prenomValid = false;      // État du champ Prénom
let emailValid = false;       // État du champ Email
let passwordValid = false;    // État du champ Mot de passe
let confirmValid = false;     // État du champ Confirmation

// ============================================================
// ÉTAPE 3 : FONCTION DE VALIDATION DU NOM
// Appelée à chaque fois que l'utilisateur tape dans le champ Nom
// ============================================================
function validateNom() {
    // Récupérer la valeur du champ et enlever les espaces
    const nom = nomInput.value.trim();
    
    // --- CAS 1 : Champ vide (pas encore tapé) ---
    if (nom === '') {
        // Enlever les couleurs (vert/rouge) du champ
        nomInput.classList.remove('valid', 'invalid');
        // Effacer le message
        nomMessage.textContent = '';
        // Enlever les couleurs du message
        nomMessage.classList.remove('valid', 'invalid');
        // Marquer comme NON valide
        nomValid = false;
    }
    // --- CAS 2 : Nom trop court (moins de 2 caractères) ---
    else if (nom.length < 2) {
        // Champ en rouge
        nomInput.classList.add('invalid');
        nomInput.classList.remove('valid');
        // Message d'erreur
        nomMessage.textContent = '❌ Minimum 2 caractères';
        // Message en rouge
        nomMessage.classList.add('invalid');
        nomMessage.classList.remove('valid');
        // Marquer comme NON valide
        nomValid = false;
    }
    // --- CAS 3 : Nom valide (2 caractères ou plus) ---
    else {
        // Champ en vert
        nomInput.classList.add('valid');
        nomInput.classList.remove('invalid');
        // Message de succès
        nomMessage.textContent = '✅ Nom valide';
        // Message en vert
        nomMessage.classList.add('valid');
        nomMessage.classList.remove('invalid');
        // Marquer comme valide
        nomValid = true;
    }
    // Vérifier si on peut activer le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 4 : FONCTION DE VALIDATION DU PRÉNOM
// Même logique que pour le nom
// ============================================================
function validatePrenom() {
    // Récupérer la valeur et nettoyer
    const prenom = prenomInput.value.trim();
    
    // --- Champ vide ---
    if (prenom === '') {
        prenomInput.classList.remove('valid', 'invalid');
        prenomMessage.textContent = '';
        prenomMessage.classList.remove('valid', 'invalid');
        prenomValid = false;
    }
    // --- Trop court ---
    else if (prenom.length < 2) {
        prenomInput.classList.add('invalid');
        prenomInput.classList.remove('valid');
        prenomMessage.textContent = '❌ Minimum 2 caractères';
        prenomMessage.classList.add('invalid');
        prenomMessage.classList.remove('valid');
        prenomValid = false;
    }
    // --- Valide ---
    else {
        prenomInput.classList.add('valid');
        prenomInput.classList.remove('invalid');
        prenomMessage.textContent = '✅ Prénom valide';
        prenomMessage.classList.add('valid');
        prenomMessage.classList.remove('invalid');
        prenomValid = true;
    }
    // Vérifier le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 5 : FONCTION DE VALIDATION DE L'EMAIL
// Vérifie que l'email a un format valide (xxx@xxx.xxx)
// ============================================================
function validateEmail() {
    // Récupérer la valeur et nettoyer
    const email = emailInput.value.trim();
    // Format accepté : quelque chose @ quelque chose . quelque chose
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // --- Champ vide ---
    if (email === '') {
        emailInput.classList.remove('valid', 'invalid');
        emailMessage.textContent = '';
        emailMessage.classList.remove('valid', 'invalid');
        emailValid = false;
    }
    // --- Format invalide ---
    else if (!emailRegex.test(email)) {
        emailInput.classList.add('invalid');
        emailInput.classList.remove('valid');
        emailMessage.textContent = '❌ Format email invalide';
        emailMessage.classList.add('invalid');
        emailMessage.classList.remove('valid');
        emailValid = false;
    }
    // --- Format valide ---
    else {
        emailInput.classList.add('valid');
        emailInput.classList.remove('invalid');
        emailMessage.textContent = '✅ Email valide';
        emailMessage.classList.add('valid');
        emailMessage.classList.remove('invalid');
        emailValid = true;
    }
    // Vérifier le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 6 : FONCTION DE VALIDATION DU MOT DE PASSE
// Vérifie la longueur : < 4 = trop court, 4-5 = faible, ≥ 6 = fort
// ============================================================
function validatePassword() {
    // Récupérer la valeur
    const password = passwordInput.value;
    
    // --- Champ vide ---
    if (password === '') {
        passwordInput.classList.remove('valid', 'invalid');
        passwordMessage.textContent = '';
        passwordMessage.classList.remove('valid', 'invalid');
        passwordValid = false;
    }
    // --- Trop court (moins de 4 caractères) ---
    else if (password.length < 4) {
        passwordInput.classList.add('invalid');
        passwordInput.classList.remove('valid');
        passwordMessage.textContent = '❌ Mot de passe trop court (min 4)';
        passwordMessage.classList.add('invalid');
        passwordMessage.classList.remove('valid');
        passwordValid = false;
    }
    // --- Mot de passe fort (6 caractères ou plus) ---
    else if (password.length >= 6) {
        passwordInput.classList.add('valid');
        passwordInput.classList.remove('invalid');
        passwordMessage.textContent = '✅ Mot de passe fort';
        passwordMessage.classList.add('valid');
        passwordMessage.classList.remove('invalid');
        passwordValid = true;
    }
    // --- Mot de passe faible (4 ou 5 caractères) ---
    else {
        passwordInput.classList.add('valid');
        passwordInput.classList.remove('invalid');
        passwordMessage.textContent = '⚠️ Mot de passe faible';  // Attention : valide mais faible
        passwordMessage.classList.add('valid');
        passwordMessage.classList.remove('invalid');
        passwordValid = true;  // Accepté mais avec avertissement
    }
    // Revalider la confirmation (car le mot de passe a changé)
    validateConfirm();
    // Vérifier le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 7 : FONCTION DE VALIDATION DE LA CONFIRMATION
// Vérifie que la confirmation est identique au mot de passe
// ============================================================
function validateConfirm() {
    // Récupérer le mot de passe
    const password = passwordInput.value;
    // Récupérer la confirmation
    const confirm = confirmInput.value;
    
    // --- Champ vide ---
    if (confirm === '') {
        confirmInput.classList.remove('valid', 'invalid');
        confirmMessage.textContent = '';
        confirmMessage.classList.remove('valid', 'invalid');
        confirmValid = false;
    }
    // --- Ne correspond pas au mot de passe ---
    else if (password !== confirm) {
        confirmInput.classList.add('invalid');
        confirmInput.classList.remove('valid');
        confirmMessage.textContent = '❌ Les mots de passe ne correspondent pas';
        confirmMessage.classList.add('invalid');
        confirmMessage.classList.remove('valid');
        confirmValid = false;
    }
    // --- Correspond parfaitement ---
    else {
        confirmInput.classList.add('valid');
        confirmInput.classList.remove('invalid');
        confirmMessage.textContent = '✅ Mots de passe identiques';
        confirmMessage.classList.add('valid');
        confirmMessage.classList.remove('invalid');
        confirmValid = true;
    }
    // Vérifier le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 8 : FONCTION QUI ACTIVE/DÉSACTIVE LE BOUTON "S'INSCRIRE"
// Le bouton s'active UNIQUEMENT si TOUS les champs sont valides
// ============================================================
function updateSubmitButton() {
    // Si les 5 champs sont valides
    if (nomValid && prenomValid && emailValid && passwordValid && confirmValid) {
        submitBtn.disabled = false;        // Activer le bouton (on peut cliquer)
        submitBtn.classList.add('enabled'); // Ajouter le style violet
    } else {
        submitBtn.disabled = true;          // Désactiver le bouton (grisé)
        submitBtn.classList.remove('enabled'); // Enlever le style violet
    }
}

// ============================================================
// ÉTAPE 9 : CONNECTER CHAQUE FONCTION À SON CHAMP
// À chaque frappe dans un champ, la fonction correspondante s'exécute
// ============================================================
nomInput.addEventListener('input', validateNom);           // Champ Nom → validateNom()
prenomInput.addEventListener('input', validatePrenom);     // Champ Prénom → validatePrenom()
emailInput.addEventListener('input', validateEmail);       // Champ Email → validateEmail()
passwordInput.addEventListener('input', validatePassword); // Champ Mot de passe → validatePassword()
confirmInput.addEventListener('input', validateConfirm);   // Champ Confirmation → validateConfirm()

// ============================================================
// ÉTAPE 10 : VALIDATION AU CHARGEMENT DE LA PAGE
// Si l'utilisateur revient après une erreur, les champs sont pré-remplis
// On les revalide automatiquement
// ============================================================
window.addEventListener('load', function() {
    // Si le champ nom contient déjà quelque chose → le valider
    if (nomInput.value.trim() !== '') validateNom();
    // Si le champ prénom contient déjà quelque chose → le valider
    if (prenomInput.value.trim() !== '') validatePrenom();
    // Si le champ email contient déjà quelque chose → le valider
    if (emailInput.value.trim() !== '') validateEmail();
    // Si le champ mot de passe contient déjà quelque chose → le valider
    if (passwordInput.value !== '') validatePassword();
    // Si le champ confirmation contient déjà quelque chose → le valider
    if (confirmInput.value !== '') validateConfirm();
});
    </script>
</body>
</html>