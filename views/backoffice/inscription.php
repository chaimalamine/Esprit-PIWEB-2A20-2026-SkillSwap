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
        .form-group input { width: 100%; padding: 14px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 15px; background: #fafafa; }
        .form-group input:focus { outline: none; border-color: #7b2ff7; background: white; box-shadow: 0 0 0 4px rgba(123, 47, 247, 0.1); }
        .form-group input.valid { border-color: #10b981 !important; }
        .form-group input.invalid { border-color: #ef4444 !important; }
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
        <a href="../frontoffice/frontdessign.php"><h2>SkillSwap</h2></a>
        <div>
            <a href="../frontoffice/frontdesign.php">Accueil</a>
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
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" id="nom" placeholder="Ton nom de famille" value="<?php echo $_POST['nom'] ?? ''; ?>">
                    <div class="validation-message" id="nomMessage"></div>
                </div>

                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" id="prenom" placeholder="Ton prénom" value="<?php echo $_POST['prenom'] ?? ''; ?>">
                    <div class="validation-message" id="prenomMessage"></div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo $_POST['email'] ?? ''; ?>">
                    <div class="validation-message" id="emailMessage"></div>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••">
                    <div class="validation-message" id="mdpMessage"></div>
                </div>

                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" placeholder="••••••••">
                    <div class="validation-message" id="confirmMessage"></div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>S'inscrire</button>
            </form>

            <div class="auth-footer">
                Déjà un compte ? <a href="connexion.php">Se connecter</a>
            </div>
        </div>
    </div>

    <script>
        const nomInput = document.getElementById('nom');
        const prenomInput = document.getElementById('prenom');
        const emailInput = document.getElementById('email');
        const mdpInput = document.getElementById('mot_de_passe');
        const confirmInput = document.getElementById('confirm_mot_de_passe');
        
        const nomMessage = document.getElementById('nomMessage');
        const prenomMessage = document.getElementById('prenomMessage');
        const emailMessage = document.getElementById('emailMessage');
        const mdpMessage = document.getElementById('mdpMessage');
        const confirmMessage = document.getElementById('confirmMessage');
        
        const submitBtn = document.getElementById('submitBtn');
        
        let nomValid = false;
        let prenomValid = false;
        let emailValid = false;
        let mdpValid = false;
        let confirmValid = false;
        
        const nameRegex = /^[A-Za-zÀ-ÿ\s-]{2,}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        function validateNom() {
            const nom = nomInput.value.trim();
            
            if (nom === '') {
                nomInput.classList.remove('valid', 'invalid');
                nomMessage.textContent = '';
                nomMessage.classList.remove('valid', 'invalid');
                nomValid = false;
            } else if (!nameRegex.test(nom)) {
                nomInput.classList.add('invalid');
                nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Min 2 lettres, pas de chiffres';
                nomMessage.classList.add('invalid');
                nomMessage.classList.remove('valid');
                nomValid = false;
            } else {
                nomInput.classList.add('valid');
                nomInput.classList.remove('invalid');
                nomMessage.textContent = '✅ Nom valide';
                nomMessage.classList.add('valid');
                nomMessage.classList.remove('invalid');
                nomValid = true;
            }
            updateSubmitButton();
        }
        
        function validatePrenom() {
            const prenom = prenomInput.value.trim();
            
            if (prenom === '') {
                prenomInput.classList.remove('valid', 'invalid');
                prenomMessage.textContent = '';
                prenomMessage.classList.remove('valid', 'invalid');
                prenomValid = false;
            } else if (!nameRegex.test(prenom)) {
                prenomInput.classList.add('invalid');
                prenomInput.classList.remove('valid');
                prenomMessage.textContent = '❌ Min 2 lettres, pas de chiffres';
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
        
        function validateEmail() {
            const email = emailInput.value.trim();
            
            if (email === '') {
                emailInput.classList.remove('valid', 'invalid');
                emailMessage.textContent = '';
                emailMessage.classList.remove('valid', 'invalid');
                emailValid = false;
            } else if (!emailRegex.test(email)) {
                emailInput.classList.add('invalid');
                emailInput.classList.remove('valid');
                emailMessage.textContent = '❌ Format d\'email invalide';
                emailMessage.classList.add('invalid');
                emailMessage.classList.remove('valid');
                emailValid = false;
            } else {
                emailInput.classList.add('valid');
                emailInput.classList.remove('invalid');
                emailMessage.textContent = '✅ Email valide';
                emailMessage.classList.add('valid');
                emailMessage.classList.remove('invalid');
                emailValid = true;
            }
            updateSubmitButton();
        }
        
        function validateMdp() {
            const mdp = mdpInput.value;
            
            if (mdp === '') {
                mdpInput.classList.remove('valid', 'invalid');
                mdpMessage.textContent = '';
                mdpMessage.classList.remove('valid', 'invalid');
                mdpValid = false;
            } else if (mdp.length < 6) {
                mdpInput.classList.add('invalid');
                mdpInput.classList.remove('valid');
                mdpMessage.textContent = '❌ Mot de passe trop court (min 6 caractères)';
                mdpMessage.classList.add('invalid');
                mdpMessage.classList.remove('valid');
                mdpValid = false;
            } else {
                mdpInput.classList.add('valid');
                mdpInput.classList.remove('invalid');
                mdpMessage.textContent = '✅ Mot de passe fort';
                mdpMessage.classList.add('valid');
                mdpMessage.classList.remove('invalid');
                mdpValid = true;
            }
            validateConfirm();
            updateSubmitButton();
        }
        
        function validateConfirm() {
            const mdp = mdpInput.value;
            const confirm = confirmInput.value;
            
            if (confirm === '') {
                confirmInput.classList.remove('valid', 'invalid');
                confirmMessage.textContent = '';
                confirmMessage.classList.remove('valid', 'invalid');
                confirmValid = false;
            } else if (mdp !== confirm) {
                confirmInput.classList.add('invalid');
                confirmInput.classList.remove('valid');
                confirmMessage.textContent = '❌ Les mots de passe ne correspondent pas';
                confirmMessage.classList.add('invalid');
                confirmMessage.classList.remove('valid');
                confirmValid = false;
            } else {
                confirmInput.classList.add('valid');
                confirmInput.classList.remove('invalid');
                confirmMessage.textContent = '✅ Les mots de passe correspondent';
                confirmMessage.classList.add('valid');
                confirmMessage.classList.remove('invalid');
                confirmValid = true;
            }
            updateSubmitButton();
        }
        
        function updateSubmitButton() {
            if (nomValid && prenomValid && emailValid && mdpValid && confirmValid) {
                submitBtn.disabled = false;
                submitBtn.classList.add('enabled');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('enabled');
            }
        }
        
        nomInput.addEventListener('input', validateNom);
        prenomInput.addEventListener('input', validatePrenom);
        emailInput.addEventListener('input', validateEmail);
        mdpInput.addEventListener('input', function() {
            validateMdp();
            validateConfirm();
        });
        confirmInput.addEventListener('input', validateConfirm);
        
        window.addEventListener('load', function() {
            if (nomInput.value.trim() !== '') validateNom();
            if (prenomInput.value.trim() !== '') validatePrenom();
            if (emailInput.value.trim() !== '') validateEmail();
            if (mdpInput.value !== '') validateMdp();
            if (confirmInput.value !== '') validateConfirm();
        });
    </script>
</body>
</html>