<?php 
// ============================================================
// DÉMARRAGE DE LA SESSION + VÉRIFICATION CONNEXION
// ============================================================
session_start();

// Vérifier que l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');  // Rediriger vers connexion si pas connecté
    exit();
}
?>
<!-- ============================================================
     HTML : STRUCTURE DE LA PAGE
     ============================================================ -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil - SkillSwap</title>
    <style>
    /* ============================================================
       CSS - Mise en forme de la page (non commenté)
       ============================================================ */
    body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: #eef2f7; }
    .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
    .sidebar h2 { text-align: center; margin-bottom: 20px; }
    .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
    .sidebar a:hover { background: rgba(255,255,255,0.2); }
    .main { margin-left: 220px; padding: 20px 30px; flex: 1; }
    .topbar { background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .topbar h3 { color: #333; font-size: 22px; }
    .profile-wrapper { max-width: 1000px; margin: 0 auto; }
    .profile-card { background: white; padding: 25px 50px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); margin-bottom: 40px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 20px; }
    .form-group { margin-bottom: 22px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #444; margin-bottom: 8px; }
    .form-group input, .form-group textarea { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 15px; background: #fafafa; font-family: inherit; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #2575fc; background: white; box-shadow: 0 0 0 4px rgba(37, 117, 252, 0.1); }
    .form-group input.valid, .form-group textarea.valid { border-color: #10b981 !important; }
    .form-group input.invalid { border-color: #ef4444 !important; }
    .form-group textarea { resize: vertical; min-height: 120px; }
    .validation-message { font-size: 13px; margin-top: 6px; min-height: 20px; }
    .validation-message.valid { color: #10b981; }
    .validation-message.invalid { color: #ef4444; }
    .error-message { background: #fee2e2; color: #b91c1c; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #b91c1c; }
    .success-message { background: #d1fae5; color: #047857; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #047857; }
    .btn-group { display: flex; gap: 20px; margin-top: 20px; padding-top: 18px; border-top: 2px solid #f4f4f4; }
    .btn { padding: 14px 28px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-save { background: #2575fc; color: white; opacity: 0.5; transition: 0.3s; }
    .btn-save.enabled { opacity: 1; }
    .btn-save.enabled:hover { background: #6a11cb; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(37, 117, 252, 0.3); }
    .btn-cancel { background: #f3f4f6; color: #4b5563; }
    .btn-cancel:hover { background: #e5e7eb; }
</style>
</head>
<body>

    <!-- ============================================================
         SIDEBAR (Barre latérale de navigation)
         ============================================================ -->
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

    <!-- ============================================================
         CONTENU PRINCIPAL
         ============================================================ -->
    <div class="main">
        
        <!-- Barre du haut avec titre et bouton déconnexion -->
        <div class="topbar">
            <h3>Modifier le Profil</h3>
            <a href="http://localhost/projetwebfinal/controllers/logout.php" style="background: #ef4444; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px;">Déconnexion</a>
        </div>

        <!-- Carte contenant le formulaire -->
        <div class="profile-wrapper">
            <div class="profile-card">
                
                <!-- ============================================================
                     AFFICHAGE DES MESSAGES DE SUCCÈS
                     ============================================================ -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <!-- ============================================================
                     AFFICHAGE DES MESSAGES D'ERREUR
                     ============================================================ -->
                <?php if(isset($_SESSION['erreur'])): ?>
                    <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
                <?php endif; ?>

                <!-- ============================================================
                     FORMULAIRE DE MODIFICATION DU PROFIL
                     Envoie les données à UserC.php?action=updateProfile
                     ============================================================ -->
                <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=updateProfile" method="POST" id="profileForm">
                    
                    <!-- Ligne avec Nom et Prénom côte à côte -->
                    <div class="form-row">
                        <!-- Champ Nom (pré-rempli avec la valeur actuelle) -->
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" id="nom" placeholder="Ton nom" value="<?php echo $_SESSION['user_nom'] ?? ''; ?>">
                            <!-- Message de validation (affiché par JavaScript) -->
                            <div class="validation-message" id="nomMessage"></div>
                        </div>
                        <!-- Champ Prénom (pré-rempli avec la valeur actuelle) -->
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" id="prenom" placeholder="Ton prénom" value="<?php echo $_SESSION['user_prenom'] ?? ''; ?>">
                            <!-- Message de validation (affiché par JavaScript) -->
                            <div class="validation-message" id="prenomMessage"></div>
                        </div>
                    </div>

                    <!-- Champ Email (pré-rempli avec la valeur actuelle) -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                        <!-- Message de validation (affiché par JavaScript) -->
                        <div class="validation-message" id="emailMessage"></div>
                    </div>

                    <!-- Champ Bio (pré-rempli avec la valeur actuelle, peut être vide) -->
                    <div class="form-group">
                        <label>Bio / Présentation</label>
                        <textarea name="bio" id="bio" placeholder="Décris-toi en quelques lignes..."><?php echo $_SESSION['user_bio'] ?? ''; ?></textarea>
                    </div>

                    <!-- Boutons Enregistrer et Annuler -->
                    <div class="btn-group">
                        <button type="submit" class="btn btn-save enabled" id="submitBtn">💾 Enregistrer les modifications</button>
                        <a href="profil.php" class="btn btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================
         JAVASCRIPT : Validation en temps réel du formulaire
         ============================================================ -->
    <script>
        // --- RÉCUPÉRER LES ÉLÉMENTS HTML ---
        const nomInput = document.getElementById('nom');              // Champ Nom
        const prenomInput = document.getElementById('prenom');        // Champ Prénom
        const emailInput = document.getElementById('email');          // Champ Email
        const nomMessage = document.getElementById('nomMessage');     // Message validation Nom
        const prenomMessage = document.getElementById('prenomMessage'); // Message validation Prénom
        const emailMessage = document.getElementById('emailMessage'); // Message validation Email
        const submitBtn = document.getElementById('submitBtn');       // Bouton Enregistrer
        
        // --- ÉTATS DE VALIDATION (true = valide, false = invalide) ---
        let nomValid = true;       // Le nom est déjà rempli, donc valide par défaut
        let prenomValid = true;    // Le prénom est déjà rempli, donc valide par défaut
        let emailValid = true;     // L'email est déjà rempli, donc valide par défaut
        
        // --- VALIDATION DU NOM ---
        function validateNom() {
            const nom = nomInput.value.trim();  // Récupérer la valeur et enlever les espaces
            
            if (nom === '') {
                // Champ vide → erreur
                nomInput.classList.add('invalid');
                nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Le nom est obligatoire';
                nomMessage.classList.add('invalid');
                nomMessage.classList.remove('valid');
                nomValid = false;
            } else if (nom.length < 2) {
                // Trop court → erreur
                nomInput.classList.add('invalid');
                nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Minimum 2 caractères';
                nomMessage.classList.add('invalid');
                nomMessage.classList.remove('valid');
                nomValid = false;
            } else {
                // Valide → succès
                nomInput.classList.add('valid');
                nomInput.classList.remove('invalid');
                nomMessage.textContent = '✅ Nom valide';
                nomMessage.classList.add('valid');
                nomMessage.classList.remove('invalid');
                nomValid = true;
            }
            updateSubmitButton();  // Vérifier si on active le bouton
        }
        
        // --- VALIDATION DU PRÉNOM (même logique que le nom) ---
        function validatePrenom() {
            const prenom = prenomInput.value.trim();
            
            if (prenom === '') {
                prenomInput.classList.add('invalid');
                prenomInput.classList.remove('valid');
                prenomMessage.textContent = '❌ Le prénom est obligatoire';
                prenomMessage.classList.add('invalid');
                prenomMessage.classList.remove('valid');
                prenomValid = false;
            } else if (prenom.length < 2) {
                prenomInput.classList.add('invalid');
                prenomInput.classList.remove('valid');
                prenomMessage.textContent = '❌ Minimum 2 caractères';
                prenomMessage.classList.add('invalid');
                prenomMessage.classList.remove('valid');
                prenomValid = false;
            } else {
                prenomInput.classList.add('valid');
                prenomInput.classList.remove('invalid');
                prenomMessage.textContent = '✅ Prénom valide';
                prenomMessage.classList.add('valid');
                prenomMessage.classList.remove('invalid');
                prenomValid = true;
            }
            updateSubmitButton();
        }
        
        // --- VALIDATION DE L'EMAIL ---
        function validateEmail() {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  // Format xxx@xxx.xxx
            
            if (email === '') {
                // Champ vide
                emailInput.classList.add('invalid');
                emailInput.classList.remove('valid');
                emailMessage.textContent = '❌ L\'email est obligatoire';
                emailMessage.classList.add('invalid');
                emailMessage.classList.remove('valid');
                emailValid = false;
            } else if (!emailRegex.test(email)) {
                // Format invalide
                emailInput.classList.add('invalid');
                emailInput.classList.remove('valid');
                emailMessage.textContent = '❌ Format d\'email invalide';
                emailMessage.classList.add('invalid');
                emailMessage.classList.remove('valid');
                emailValid = false;
            } else {
                // Format valide
                emailInput.classList.add('valid');
                emailInput.classList.remove('invalid');
                emailMessage.textContent = '✅ Email valide';
                emailMessage.classList.add('valid');
                emailMessage.classList.remove('invalid');
                emailValid = true;
            }
            updateSubmitButton();
        }
        
        // --- ACTIVER/DÉSACTIVER LE BOUTON ENREGISTRER ---
        function updateSubmitButton() {
            if (nomValid && prenomValid && emailValid) {
                submitBtn.disabled = false;        // Activer le bouton
                submitBtn.classList.add('enabled'); // Style violet
            } else {
                submitBtn.disabled = true;          // Désactiver le bouton
                submitBtn.classList.remove('enabled'); // Style gris
            }
        }
        
        // --- CONNECTER LES FONCTIONS AUX CHAMPS ---
        nomInput.addEventListener('input', validateNom);       // Frappe dans Nom → valider
        prenomInput.addEventListener('input', validatePrenom); // Frappe dans Prénom → valider
        emailInput.addEventListener('input', validateEmail);   // Frappe dans Email → valider
        
        // --- VALIDATION AU CHARGEMENT (champs pré-remplis) ---
        window.addEventListener('load', function() {
            validateNom();       // Valider le nom (affiche ✅ directement)
            validatePrenom();    // Valider le prénom
            validateEmail();     // Valider l'email
        });
    </script>
</body>
</html>