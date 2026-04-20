<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil - SkillSwap</title>
    <style>
    body { 
        margin: 0; 
        font-family: 'Segoe UI', sans-serif; 
        display: flex; 
        background: #eef2f7; 
    }
    
    .sidebar { 
        width: 220px; 
        background: linear-gradient(180deg, #6a11cb, #2575fc); 
        color: white; 
        height: 100vh; 
        padding: 20px; 
        position: fixed; 
    }
    
    .sidebar h2 { 
        text-align: center; 
        margin-bottom: 20px; 
    }
    
    .sidebar a { 
        display: block; 
        color: white; 
        text-decoration: none; 
        margin: 15px 0; 
        padding: 10px; 
        border-radius: 5px; 
    }
    
    .sidebar a:hover { 
        background: rgba(255,255,255,0.2); 
    }
    
    .main { 
        margin-left: 220px; 
        padding: 20px 30px; 
        flex: 1; 
    }
    
    .topbar { 
        background: white; 
        padding: 15px 25px; 
        border-radius: 12px; 
        box-shadow: 0 3px 10px rgba(0,0,0,0.08); 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
    }
    
    .topbar h3 { 
        color: #333; 
        font-size: 22px; 
    }
    
    .profile-wrapper { 
        max-width: 1000px; 
        margin: 0 auto; 
    }
    
    .profile-card { 
    background: white; 
    padding: 25px 50px;  /* ← plus d'espace à gauche et à droite */
    border-radius: 15px; 
    box-shadow: 0 5px 20px rgba(0,0,0,0.06); 
    margin-bottom: 40px;
}
    
    .form-row { 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 40px;  /* ← plus d'espace entre les deux champs */
    margin-bottom: 20px; 
}
    
    .form-group { 
        margin-bottom: 22px; 
    }
    
    .form-group label { 
        display: block; 
        font-size: 14px; 
        font-weight: 600; 
        color: #444; 
        margin-bottom: 8px; 
    }
    
    .form-group input, 
    .form-group textarea { 
        width: 100%; 
        padding: 14px 16px; 
        border: 2px solid #e8e8e8; 
        border-radius: 10px; 
        font-size: 15px; 
        background: #fafafa; 
        font-family: inherit; 
    }
    
    .form-group input:focus, 
    .form-group textarea:focus { 
        outline: none; 
        border-color: #2575fc; 
        background: white; 
        box-shadow: 0 0 0 4px rgba(37, 117, 252, 0.1); 
    }
    
    .form-group input.valid, 
    .form-group textarea.valid { 
        border-color: #10b981 !important; 
    }
    
    .form-group input.invalid { 
        border-color: #ef4444 !important; 
    }
    
    .form-group textarea { 
        resize: vertical; 
        min-height: 120px; 
    }
    
    .validation-message { 
        font-size: 13px; 
        margin-top: 6px; 
        min-height: 20px; 
    }
    
    .validation-message.valid { 
        color: #10b981; 
    }
    
    .validation-message.invalid { 
        color: #ef4444; 
    }
    
    .error-message { 
        background: #fee2e2; 
        color: #b91c1c; 
        padding: 14px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
        font-size: 14px; 
        border-left: 4px solid #b91c1c; 
    }
    
    .success-message { 
        background: #d1fae5; 
        color: #047857; 
        padding: 14px; 
        border-radius: 8px; 
        margin-bottom: 20px; 
        font-size: 14px; 
        border-left: 4px solid #047857; 
    }
    
    .btn-group { 
        display: flex; 
        gap: 20px; 
        margin-top: 20px; 
        padding-top: 18px; 
        border-top: 2px solid #f4f4f4; 
    }
    
    .btn { 
        padding: 14px 28px; 
        border: none; 
        border-radius: 10px; 
        font-size: 15px; 
        font-weight: 600; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-block; 
    }
    
    .btn-save { 
        background: #2575fc; 
        color: white; 
        opacity: 0.5; 
        transition: 0.3s; 
    }
    
    .btn-save.enabled { 
        opacity: 1; 
    }
    
    .btn-save.enabled:hover { 
        background: #6a11cb; 
        transform: translateY(-2px); 
        box-shadow: 0 6px 15px rgba(37, 117, 252, 0.3); 
    }
    
    .btn-cancel { 
        background: #f3f4f6; 
        color: #4b5563; 
    }
    
    .btn-cancel:hover { 
        background: #e5e7eb; 
    }
</style>
</head>
<body>
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

    <div class="main">
        <div class="topbar">
            <h3>Modifier le Profil</h3>
            <a href="http://localhost/projetwebfinal/controllers/logout.php" style="background: #ef4444; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px;">Déconnexion</a>
        </div>

        <div class="profile-wrapper">
            <div class="profile-card">
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['erreur'])): ?>
                    <div class="error-message"><?php echo $_SESSION['erreur']; unset($_SESSION['erreur']); ?></div>
                <?php endif; ?>

                <form action="http://localhost/projetwebfinal/controllers/UserC.php?action=updateProfile" method="POST" id="profileForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" id="nom" placeholder="Ton nom" value="<?php echo $_SESSION['user_nom'] ?? ''; ?>">
                            <div class="validation-message" id="nomMessage"></div>
                        </div>
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" id="prenom" placeholder="Ton prénom" value="<?php echo $_SESSION['user_prenom'] ?? ''; ?>">
                            <div class="validation-message" id="prenomMessage"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" id="email" placeholder="exemple@email.com" value="<?php echo $_SESSION['user_email'] ?? ''; ?>">
                        <div class="validation-message" id="emailMessage"></div>
                    </div>

                    <div class="form-group">
                        <label>Bio / Présentation</label>
                        <textarea name="bio" id="bio" placeholder="Décris-toi en quelques lignes..."><?php echo $_SESSION['user_bio'] ?? ''; ?></textarea>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-save enabled" id="submitBtn">💾 Enregistrer les modifications</button>
                        <a href="profil.php" class="btn btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const nomInput = document.getElementById('nom');
        const prenomInput = document.getElementById('prenom');
        const emailInput = document.getElementById('email');
        const nomMessage = document.getElementById('nomMessage');
        const prenomMessage = document.getElementById('prenomMessage');
        const emailMessage = document.getElementById('emailMessage');
        const submitBtn = document.getElementById('submitBtn');
        
        let nomValid = true;
        let prenomValid = true;
        let emailValid = true;
        
        function validateNom() {
            const nom = nomInput.value.trim();
            
            if (nom === '') {
                nomInput.classList.add('invalid');
                nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Le nom est obligatoire';
                nomMessage.classList.add('invalid');
                nomMessage.classList.remove('valid');
                nomValid = false;
            } else if (nom.length < 2) {
                nomInput.classList.add('invalid');
                nomInput.classList.remove('valid');
                nomMessage.textContent = '❌ Minimum 2 caractères';
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
        
        function validateEmail() {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email === '') {
                emailInput.classList.add('invalid');
                emailInput.classList.remove('valid');
                emailMessage.textContent = '❌ L\'email est obligatoire';
                emailMessage.classList.add('invalid');
                emailMessage.classList.remove('valid');
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
        
        function updateSubmitButton() {
            if (nomValid && prenomValid && emailValid) {
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
        
        window.addEventListener('load', function() {
            validateNom();
            validatePrenom();
            validateEmail();
        });
    </script>
</body>
</html>