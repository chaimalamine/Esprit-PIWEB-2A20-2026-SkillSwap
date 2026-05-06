<?php
// Pas de session_start() car déjà fait dans frontdessign.php
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>

<style>
    .profile-wrapper-mf { max-width: 800px; margin: 0 auto; }
    .profile-card-mf { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
    .form-row-mf { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px; }
    .form-group-mf { margin-bottom: 22px; }
    .form-group-mf label { display: block; font-size: 14px; font-weight: 600; color: #444; margin-bottom: 8px; }
    .form-group-mf input, .form-group-mf textarea { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 15px; background: #fafafa; font-family: inherit; }
    .form-group-mf input:focus, .form-group-mf textarea:focus { outline: none; border-color: #7b2ff7; background: white; }
    .form-group-mf input.valid, .form-group-mf textarea.valid { border-color: #10b981 !important; }
    .form-group-mf input.invalid { border-color: #ef4444 !important; }
    .form-group-mf textarea { resize: vertical; min-height: 120px; }
    .validation-message-mf { font-size: 13px; margin-top: 6px; min-height: 20px; }
    .validation-message-mf.valid { color: #10b981; }
    .validation-message-mf.invalid { color: #ef4444; }
    .error-message-mf { background: #fee2e2; color: #b91c1c; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
    .success-message-mf { background: #d1fae5; color: #047857; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
    .btn-group-mf { display: flex; gap: 20px; margin-top: 20px; padding-top: 18px; border-top: 2px solid #f4f4f4; }
    .btn-mf { padding: 14px 28px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-save-mf { background: linear-gradient(90deg, #7b2ff7, #a855f7); color: white; opacity: 0.5; transition: 0.3s; }
    .btn-save-mf.enabled { opacity: 1; }
    .btn-save-mf.enabled:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(123, 47, 247, 0.3); }
    .btn-cancel-mf { background: #f3f4f6; color: #4b5563; }
    .btn-cancel-mf:hover { background: #e5e7eb; }
</style>

<div class="profile-wrapper-mf">
    <div class="profile-card-mf">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message-mf"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['erreur'])): ?>
            <div class="error-message-mf"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
        <?php endif; ?>

        <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=updateProfile" method="POST" id="profileForm">
             <input type="hidden" name="from_front" value="1">
            <div class="form-row-mf">
                <div class="form-group-mf">
                    <label>Nom</label>
                    <input type="text" name="nom" id="nom" placeholder="Ton nom" value="<?php echo $_SESSION['user_nom'] ?? ''; ?>">
                    <div class="validation-message-mf" id="nomMessage"></div>
                </div>
                <div class="form-group-mf">
                    <label>Prénom</label>
                    <input type="text" name="prenom" id="prenom" placeholder="Ton prénom" value="<?php echo $_SESSION['user_prenom'] ?? ''; ?>">
                    <div class="validation-message-mf" id="prenomMessage"></div>
                </div>
            </div>

            <div class="form-group-mf">
                <label>Email</label>
                <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                <div class="validation-message-mf" id="emailMessage"></div>
            </div>

            <div class="form-group-mf">
                <label>Bio / Présentation</label>
                <textarea name="bio" id="bio" placeholder="Décris-toi en quelques lignes..."><?php echo $_SESSION['user_bio'] ?? ''; ?></textarea>
            </div>

            <div class="btn-group-mf">
                <button type="submit" class="btn-mf btn-save-mf enabled" id="submitBtn">💾 Enregistrer les modifications</button>
                <a href="frontdessign.php?page=profil" class="btn-mf btn-cancel-mf">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================================
// ÉTAPE 1 : RÉCUPÉRER LES ÉLÉMENTS HTML PAR LEUR ID
// ============================================================

// Champs du formulaire (pré-remplis avec les valeurs actuelles)
const nomInput = document.getElementById('nom');            // Champ "Nom"
const prenomInput = document.getElementById('prenom');      // Champ "Prénom"
const emailInput = document.getElementById('email');        // Champ "Email"

// Messages de validation (affichés sous chaque champ)
const nomMessage = document.getElementById('nomMessage');       // Message pour le nom
const prenomMessage = document.getElementById('prenomMessage'); // Message pour le prénom
const emailMessage = document.getElementById('emailMessage');   // Message pour l'email

// Bouton "Enregistrer les modifications"
const submitBtn = document.getElementById('submitBtn');

// ============================================================
// ÉTAPE 2 : VARIABLES D'ÉTAT
// Initialisées à TRUE car les champs sont déjà pré-remplis
// ============================================================
let nomValid = true;         // Le nom est déjà valide au chargement
let prenomValid = true;      // Le prénom est déjà valide au chargement
let emailValid = true;       // L'email est déjà valide au chargement

// ============================================================
// ÉTAPE 3 : FONCTION DE VALIDATION DU NOM
// Appelée quand l'utilisateur modifie le champ
// ============================================================
function validateNom() {
    // Récupérer la valeur et enlever les espaces
    const nom = nomInput.value.trim();
    
    // --- CAS 1 : Champ vide ---
    if (nom === '') {
        // Champ en rouge
        nomInput.classList.add('invalid');
        nomInput.classList.remove('valid');
        // Message d'erreur
        nomMessage.textContent = '❌ Le nom est obligatoire';
        nomMessage.classList.add('invalid');
        nomMessage.classList.remove('valid');
        // NON valide
        nomValid = false;
    }
    // --- CAS 2 : Trop court (moins de 2 caractères) ---
    else if (nom.length < 2) {
        // Champ en rouge
        nomInput.classList.add('invalid');
        nomInput.classList.remove('valid');
        // Message d'erreur
        nomMessage.textContent = '❌ Minimum 2 caractères';
        nomMessage.classList.add('invalid');
        nomMessage.classList.remove('valid');
        // NON valide
        nomValid = false;
    }
    // --- CAS 3 : Nom valide ---
    else {
        // Champ en vert
        nomInput.classList.add('valid');
        nomInput.classList.remove('invalid');
        // Message de succès
        nomMessage.textContent = '✅ Nom valide';
        nomMessage.classList.add('valid');
        nomMessage.classList.remove('invalid');
        // Valide
        nomValid = true;
    }
    // Vérifier si on active le bouton
    updateSubmitButton();
}

// ============================================================
// ÉTAPE 4 : FONCTION DE VALIDATION DU PRÉNOM
// Même logique que le nom
// ============================================================
function validatePrenom() {
    // Récupérer la valeur et nettoyer
    const prenom = prenomInput.value.trim();
    
    // --- Champ vide ---
    if (prenom === '') {
        prenomInput.classList.add('invalid');
        prenomInput.classList.remove('valid');
        prenomMessage.textContent = '❌ Le prénom est obligatoire';
        prenomMessage.classList.add('invalid');
        prenomMessage.classList.remove('valid');
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
// Vérifie le format xxx@xxx.xxx
// ============================================================
function validateEmail() {
    // Récupérer la valeur et nettoyer
    const email = emailInput.value.trim();
    // Format accepté
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // --- Champ vide ---
    if (email === '') {
        emailInput.classList.add('invalid');
        emailInput.classList.remove('valid');
        emailMessage.textContent = '❌ L\'email est obligatoire';
        emailMessage.classList.add('invalid');
        emailMessage.classList.remove('valid');
        emailValid = false;
    }
    // --- Format invalide ---
    else if (!emailRegex.test(email)) {
        emailInput.classList.add('invalid');
        emailInput.classList.remove('valid');
        emailMessage.textContent = '❌ Format d\'email invalide';
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
// ÉTAPE 6 : FONCTION QUI ACTIVE/DÉSACTIVE LE BOUTON
// ============================================================
function updateSubmitButton() {
    // Si les 3 champs sont valides
    if (nomValid && prenomValid && emailValid) {
        submitBtn.disabled = false;        // Activer le bouton
        submitBtn.classList.add('enabled'); // Style violet
    } else {
        submitBtn.disabled = true;          // Désactiver le bouton
        submitBtn.classList.remove('enabled'); // Style gris
    }
}

// ============================================================
// ÉTAPE 7 : CONNECTER LES FONCTIONS AUX CHAMPS
// ============================================================
nomInput.addEventListener('input', validateNom);         // Frappe dans Nom → validateNom()
prenomInput.addEventListener('input', validatePrenom);   // Frappe dans Prénom → validatePrenom()
emailInput.addEventListener('input', validateEmail);     // Frappe dans Email → validateEmail()

// ============================================================
// ÉTAPE 8 : VALIDATION AU CHARGEMENT DE LA PAGE
// Les champs sont pré-remplis, on les valide immédiatement
// ============================================================
window.addEventListener('load', function() {
    validateNom();       // Valider le nom (affichage vert direct)
    validatePrenom();    // Valider le prénom
    validateEmail();     // Valider l'email
});
</script>