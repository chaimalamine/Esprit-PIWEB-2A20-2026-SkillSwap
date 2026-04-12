<?php 
session_start();
include '../../controllers/UserC.php';

$userC = new UserC();

// --- SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $userC->deleteUser($id);
    header('Location: liste_users.php');
    exit();
}

// --- MODIFICATION ---
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $statut = $_POST['statut'];
    $score = $_POST['score_reputation'];
    $badge = $_POST['badge_confiance'];

    if ($nom != '' && $prenom != '' && $email != '') {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setStatut($statut);
        $user->setScore_reputation($score);
        $user->setBadge_confiance($badge);
        
        $userC->updateUser($user, $id);
    }
    header('Location: liste_users.php');
    exit();
}

// --- RECHERCHE ET TRI ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$tri = isset($_GET['tri']) ? $_GET['tri'] : '';

if ($search != '') {
    $users = $userC->searchUser($search);
} elseif ($tri != '') {
    $users = $userC->sortUser($tri);
} else {
    $users = $userC->listeUser();
}

if (is_object($users)) {
    $liste = array();
    while ($row = $users->fetch()) {
        $liste[] = $row;
    }
    $users = $liste;
}

$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs - SkillSwap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8f9fc; }
        
        .sidebar { width: 240px; background: linear-gradient(180deg, #a8b5f5 0%, #c4b5fd 100%); color: #4c1d95; height: 100vh; padding: 25px 15px; position: fixed; box-shadow: 2px 0 8px rgba(0,0,0,0.05); }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 24px; }
        .sidebar a { display: block; color: #5b21b6; text-decoration: none; margin: 10px 0; padding: 10px 15px; border-radius: 8px; font-weight: 500; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.5); transform: translateX(4px); }
        
        .main { margin-left: 240px; padding: 30px; }
        .header { background: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .header h2 { color: #4c1d95; font-size: 24px; }
        
        .filters { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .filters input, .filters select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
        .filters input:focus { outline: none; border-color: #a8b5f5; }
        .filters button { padding: 8px 16px; background: #a8b5f5; color: #4c1d95; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .filters a { padding: 8px 16px; background: #cbd5e1; color: #475569; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; }
        
        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #64748b; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
        tr:hover { background: #fafafa; }
        
        td input, td select { width: 100%; padding: 6px 8px; border: 1.5px solid #cbd5e1; border-radius: 5px; font-size: 13px; background: #fff; }
        td input:focus, td select:focus { border-color: #a8b5f5; outline: none; }
        
        .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 12px; font-weight: 600; margin: 0 2px; transition: 0.2s; }
        .btn-edit { background: #93c5fd; color: #1e40af; }
        .btn-delete { background: #fca5a5; color: #991b1b; }
        .btn-save { background: #86efac; color: #166534; }
        .btn-cancel { background: #e2e8f0; color: #475569; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-actif { background: #bbf7d0; color: #166534; }
        .badge-inactif { background: #fecaca; color: #991b1b; }
        
        .empty { text-align: center; padding: 30px; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>SkillSwap</h2>
        <a href="design.php">Dashboard</a>
        <a href="liste_users.php" class="active">Gestion Users</a>
        <a href="profil.php">Mon Profil</a>
    </div>

    <div class="main">
        <div class="header">
            <h2>Liste des Utilisateurs</h2>
        </div>

        <div class="filters">
            <form method="GET" action="" style="display:flex; gap:8px; flex:1;">
                <input type="text" name="search" placeholder="🔍 Rechercher..." value="<?php echo $search; ?>">
                <button type="submit">OK</button>
            </form>
            <form method="GET" action="" style="display:flex; gap:8px;">
                <select name="tri">
                    <option value="">Trier par...</option>
                    <option value="nom" <?php if($tri=='nom') echo 'selected'; ?>>Nom (A-Z)</option>
                    <option value="prenom" <?php if($tri=='prenom') echo 'selected'; ?>>Prénom</option>
                    <option value="date_inscription" <?php if($tri=='date_inscription') echo 'selected'; ?>>Date inscription</option>
                    <option value="score_reputation" <?php if($tri=='score_reputation') echo 'selected'; ?>>Score</option>
                </select>
                <button type="submit">Trier</button>
            </form>
            <?php if($search != '' || $tri != ''): ?>
                <a href="liste_users.php">Réinitialiser</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Score</th>
                        <th>Badge</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                            <?php if ($edit_id == $u['id_utilisateur']): ?>
                                <tr style="background:#f0f4ff;">
                                    <form method="POST" action="" style="display:contents;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?php echo $u['id_utilisateur']; ?>">
                                        <td><?php echo $u['id_utilisateur']; ?></td>
                                        <td><input type="text" name="nom" value="<?php echo $u['nom']; ?>"></td>
                                        <td><input type="text" name="prenom" value="<?php echo $u['prenom']; ?>"></td>
                                        <td><input type="text" name="email" value="<?php echo $u['email']; ?>"></td>
                                        <td>
                                            <select name="statut">
                                                <option value="Actif" <?php if($u['statut']=='Actif') echo 'selected'; ?>>Actif</option>
                                                <option value="Inactif" <?php if($u['statut']=='Inactif') echo 'selected'; ?>>Inactif</option>
                                            </select>
                                        </td>
                                        <td><input type="number" name="score_reputation" value="<?php echo $u['score_reputation']; ?>" min="0" style="width:80px;"></td>
                                        <td>
                                            <select name="badge_confiance">
                                                <option value="Debutant" <?php if($u['badge_confiance']=='Debutant') echo 'selected'; ?>>Débutant</option>
                                                <option value="Bronze" <?php if($u['badge_confiance']=='Bronze') echo 'selected'; ?>>Bronze</option>
                                                <option value="Argent" <?php if($u['badge_confiance']=='Argent') echo 'selected'; ?>>Argent</option>
                                                <option value="Or" <?php if($u['badge_confiance']=='Or') echo 'selected'; ?>>Or</option>
                                                <option value="Platine" <?php if($u['badge_confiance']=='Platine') echo 'selected'; ?>>Platine</option>
                                            </select>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($u['date_inscription'])); ?></td>
                                        <td>
                                            <button type="submit" class="btn btn-save">✅</button>
                                            <a href="liste_users.php" class="btn btn-cancel">❌</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><?php echo $u['id_utilisateur']; ?></td>
                                    <td><?php echo $u['nom']; ?></td>
                                    <td><?php echo $u['prenom']; ?></td>
                                    <td><?php echo $u['email']; ?></td>
                                    <td><span class="badge <?php echo ($u['statut']=='Actif') ? 'badge-actif' : 'badge-inactif'; ?>"><?php echo $u['statut']; ?></span></td>
                                    <td>⭐ <?php echo $u['score_reputation']; ?></td>
                                    <td><?php echo $u['badge_confiance']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($u['date_inscription'])); ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $u['id_utilisateur']; ?>" class="btn btn-edit">✏️</a>
                                        <a href="?action=delete&id=<?php echo $u['id_utilisateur']; ?>" class="btn btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">🗑</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="empty">Aucun utilisateur trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>