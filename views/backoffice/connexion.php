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
        <a href="../frontoffice/frontdesign.php"><h2>SkillSwap</h2></a>
        <div>
            <a href="../frontoffice/frontdesign.php">Accueil</a>
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
                    <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo $_POST['email'] ?? ''; ?>">
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
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailMessage = document.getElementById('emailMessage');
        const passwordMessage = document.getElementById('passwordMessage');
        const submitBtn = document.getElementById('submitBtn');
        
        let emailValid = false;
        let passwordValid = false;
        
        function validateEmail() {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
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
        
        function validatePassword() {
            const password = passwordInput.value;
            
            if (password === '') {
                passwordInput.classList.remove('valid', 'invalid');
                passwordMessage.textContent = '';
                passwordMessage.classList.remove('valid', 'invalid');
                passwordValid = false;
            } else {
                passwordInput.classList.add('valid');
                passwordInput.classList.remove('invalid');
                passwordMessage.textContent = '✅ Mot de passe saisi';
                passwordMessage.classList.add('valid');
                passwordMessage.classList.remove('invalid');
                passwordValid = true;
            }
            updateSubmitButton();
        }
        
        function updateSubmitButton() {
            if (emailValid && passwordValid) {
                submitBtn.disabled = false;
                submitBtn.classList.add('enabled');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('enabled');
            }
        }
        
        emailInput.addEventListener('input', validateEmail);
        passwordInput.addEventListener('input', validatePassword);
        
        window.addEventListener('load', function() {
            if (emailInput.value.trim() !== '') {
                validateEmail();
            }
            if (passwordInput.value !== '') {
                validatePassword();
            }
        });
    </script>
</body>
</html>