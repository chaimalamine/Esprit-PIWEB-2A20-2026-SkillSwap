<?php
// ============================================================
// VUE ADMIN - Liste de tous les parrainages
// ============================================================
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/frontdessign.php');
    exit();
}

include_once '../../controllers/ParrainageC.php';
$parrainageC = new ParrainageC();

$parrainages  = $parrainageC->getAllParrainages();
$stats        = $parrainageC->getStatsGlobales();

// Filtre statut
$filtreStatut = isset($_GET['statut']) ? $_GET['statut'] : '';
if ($filtreStatut !== '') {
    $parrainages = array_filter($parrainages, function($p) use ($filtreStatut) {
        return $p['statut'] === $filtreStatut;
    });
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Parrainages - SkillSwap Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f7; display: flex; }

        /* Sidebar */
        .sidebar { width: 220px; background: linear-gradient(180deg, #6a11cb, #2575fc); color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }

        /* Main */
        .main { margin-left: 220px; padding: 25px; flex: 1; }
        .header { background: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .header h2 { color: #4c1d95; font-size: 24px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-align: center; }
        .stat-card h4 { color: #666; font-size: 12px; margin-bottom: 8px; text-transform: uppercase; }
        .stat-card .num { font-size: 32px; font-weight: bold; color: #7b2ff7; }

        /* Filtres */
        .filters { background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .filters select, .filters input { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; }
        .filters button { padding: 8px 16px; background: #7b2ff7; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .filters a { padding: 8px 16px; background: #e2e8f0; color: #475569; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; }

        /* Tableau */
        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #64748b; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
        tr:hover { background: #fafafa; }
        .empty { text-align: center; padding: 40px; color: #94a3b8; font-style: italic; }

        /* Badges */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-attente  { background: #fef3c7; color: #92400e; }
        .badge-accepte  { background: #bbf7d0; color: #166534; }
        .badge-expire   { background: #f1f5f9; color: #64748b; }

        /* Boutons */
        .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 12px; font-weight: 600; margin: 0 2px; transition: 0.2s; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .btn-delete { background: #fca5a5; color: #991b1b; }

        /* Messages */
        .msg-success { background: #d1fae5; color: #047857; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; border-left: 4px solid #047857; font-size: 14px; }

        /* Code */
        .code-mono { font-family: monospace; font-weight: bold; color: #7b2ff7; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="design.php">Dashboard</a>
    <a href="liste_users.php">Utilisateurs</a>
    <a href="mes_competences.php">Mes Compétences</a>
    <a href="liste_parrainages.php" class="active">Parrainages</a>
    <a href="profil.php">Profil</a>
    <a href="../frontoffice/frontdessign.php" style="margin-top: 30px; background: rgba(255,255,255,0.1);">← Retour au site</a>
</div>

<div class="main">
    <div class="header"><h2>🤝 Gestion des Parrainages</h2></div>

    <?php if (isset($_SESSION['success_parrainage'])): ?>
        <div class="msg-success">✅ <?php echo htmlspecialchars($_SESSION['success_parrainage']); unset($_SESSION['success_parrainage']); ?></div>
    <?php endif; ?>

    <!-- Statistiques globales -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total</h4>
            <div class="num"><?php echo $stats['total'] ?? 0; ?></div>
        </div>
        <div class="stat-card">
            <h4>Acceptés</h4>
            <div class="num" style="color:#10b981;"><?php echo $stats['acceptees'] ?? 0; ?></div>
        </div>
        <div class="stat-card">
            <h4>En attente</h4>
            <div class="num" style="color:#f59e0b;"><?php echo $stats['en_attente'] ?? 0; ?></div>
        </div>
        <div class="stat-card">
            <h4>Expirés</h4>
            <div class="num" style="color:#94a3b8;"><?php echo $stats['expirees'] ?? 0; ?></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <form method="GET" action="" style="display:flex; gap:8px; align-items:center;">
            <select name="statut">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?php if($filtreStatut=='en_attente') echo 'selected'; ?>>En attente</option>
                <option value="accepte"    <?php if($filtreStatut=='accepte')    echo 'selected'; ?>>Accepté</option>
                <option value="expire"     <?php if($filtreStatut=='expire')     echo 'selected'; ?>>Expiré</option>
            </select>
            <button type="submit">Filtrer</button>
        </form>
        <?php if ($filtreStatut !== ''): ?>
            <a href="liste_parrainages.php">Réinitialiser</a>
        <?php endif; ?>
    </div>

    <!-- Tableau -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Parrain</th>
                    <th>Email invité</th>
                    <th>Code</th>
                    <th>Statut</th>
                    <th>Filleul</th>
                    <th>Date invitation</th>
                    <th>Date acceptation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($parrainages && count($parrainages) > 0): ?>
                    <?php foreach ($parrainages as $p): ?>
                        <tr>
                            <td><?php echo $p['id_parrainage']; ?></td>
                            <td><?php echo htmlspecialchars($p['prenom_parrain'] . ' ' . $p['nom_parrain']); ?></td>
                            <td><?php echo htmlspecialchars($p['email_invite']); ?></td>
                            <td><span class="code-mono"><?php echo htmlspecialchars($p['code_parrainage']); ?></span></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-attente';
                                $label = 'En attente';
                                if ($p['statut'] === 'accepte') { $badgeClass = 'badge-accepte'; $label = 'Accepté ✅'; }
                                elseif ($p['statut'] === 'expire') { $badgeClass = 'badge-expire'; $label = 'Expiré'; }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $label; ?></span>
                            </td>
                            <td>
                                <?php
                                if ($p['nom_filleul']) {
                                    echo htmlspecialchars($p['prenom_filleul'] . ' ' . $p['nom_filleul']);
                                } else {
                                    echo '<span style="color:#94a3b8;">—</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['date_invitation'])); ?></td>
                            <td>
                                <?php echo $p['date_acceptation']
                                    ? date('d/m/Y H:i', strtotime($p['date_acceptation']))
                                    : '<span style="color:#94a3b8;">—</span>'; ?>
                            </td>
                            <td>
                                <a href="../../controllers/ParrainageC.php?action=supprimer_admin&id=<?php echo $p['id_parrainage']; ?>"
                                   class="btn btn-delete"
                                   onclick="return confirm('Supprimer ce parrainage ?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="empty">Aucun parrainage trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
