<?php
// ============================================================
// FICHIER INCLUS DANS FRONTDESSIGN.PHP
// Gère le module parrainage côté utilisateur
// ============================================================

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

include_once __DIR__ . '/../../controllers/ParrainageC.php';
$parrainageC = new ParrainageC();

$invitations = $parrainageC->getInvitationsByParrain($_SESSION['user_id']);
$stats       = $parrainageC->getStatsByParrain($_SESSION['user_id']);
?>

<style>
    /* ---- Cartes stats ---- */
    .stats-parr { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
    .stat-parr  { background: white; padding: 18px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; }
    .stat-parr h4 { color: #666; font-size: 12px; margin-bottom: 8px; text-transform: uppercase; }
    .stat-parr .num { font-size: 30px; font-weight: bold; color: #7b2ff7; }

    /* ---- Formulaire invitation ---- */
    .form-parr { background: white; padding: 25px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
    .form-parr h3 { color: #4c1d95; margin-bottom: 18px; font-size: 18px; }
    .input-row-parr { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .input-row-parr input[type="email"] {
        flex: 1; min-width: 220px; padding: 11px 15px;
        border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px;
    }
    .input-row-parr input[type="email"]:focus { outline: none; border-color: #7b2ff7; }
    .btn-inviter {
        padding: 11px 28px; background: linear-gradient(90deg, #7b2ff7, #a855f7);
        color: white; border: none; border-radius: 8px; font-weight: 600;
        font-size: 14px; cursor: pointer; transition: transform 0.2s;
    }
    .btn-inviter:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(123,47,247,0.3); }

    /* ---- Code de parrainage ---- */
    .code-box {
        background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        border: 2px dashed #a855f7; border-radius: 12px;
        padding: 18px 25px; margin-bottom: 25px; display: flex;
        align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .code-box .label { color: #6b21a8; font-weight: 600; font-size: 14px; }
    .code-box .code  { font-size: 22px; font-weight: bold; color: #7b2ff7; letter-spacing: 3px; }
    .btn-copy {
        padding: 8px 18px; background: #7b2ff7; color: white;
        border: none; border-radius: 6px; cursor: pointer; font-size: 13px;
    }
    .btn-copy:hover { background: #6d28d9; }

    /* ---- Messages ---- */
    .msg-success-parr { background: #d1fae5; color: #047857; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; border-left: 4px solid #047857; font-size: 14px; }
    .msg-error-parr   { background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; border-left: 4px solid #b91c1c; font-size: 14px; }

    /* ---- Tableau invitations ---- */
    .table-parr { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .table-parr table { width: 100%; border-collapse: collapse; }
    .table-parr th { background: #f1f5f9; color: #64748b; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 600; }
    .table-parr td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
    .table-parr tr:hover { background: #fafafa; }
    .table-parr .empty { text-align: center; padding: 40px; color: #94a3b8; font-style: italic; }

    /* ---- Badges statut ---- */
    .badge-parr { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-attente  { background: #fef3c7; color: #92400e; }
    .badge-accepte  { background: #bbf7d0; color: #166534; }
    .badge-expire   { background: #f1f5f9; color: #64748b; }

    /* ---- Bouton annuler ---- */
    .btn-annuler-parr {
        padding: 4px 10px; background: #fee2e2; color: #b91c1c;
        border: none; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: 600;
        text-decoration: none; display: inline-block;
    }
    .btn-annuler-parr:hover { background: #fecaca; }

    /* ---- Titre section ---- */
    .section-title-parr { color: #4c1d95; font-size: 18px; font-weight: 700; margin-bottom: 15px; }

    @media (max-width: 700px) {
        .stats-parr { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<!-- Messages flash -->
<?php if (isset($_SESSION['success_parrainage'])): ?>
    <div class="msg-success-parr">✅ <?php echo htmlspecialchars($_SESSION['success_parrainage']); unset($_SESSION['success_parrainage']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['erreur_parrainage'])): ?>
    <div class="msg-error-parr">❌ <?php echo htmlspecialchars($_SESSION['erreur_parrainage']); unset($_SESSION['erreur_parrainage']); ?></div>
<?php endif; ?>

<!-- Statistiques -->
<div class="stats-parr">
    <div class="stat-parr">
        <h4>Total invitations</h4>
        <div class="num"><?php echo $stats['total_invitations'] ?? 0; ?></div>
    </div>
    <div class="stat-parr">
        <h4>Acceptées</h4>
        <div class="num" style="color:#10b981;"><?php echo $stats['acceptees'] ?? 0; ?></div>
    </div>
    <div class="stat-parr">
        <h4>En attente</h4>
        <div class="num" style="color:#f59e0b;"><?php echo $stats['en_attente'] ?? 0; ?></div>
    </div>
    <div class="stat-parr">
        <h4>Expirées</h4>
        <div class="num" style="color:#94a3b8;"><?php echo $stats['expirees'] ?? 0; ?></div>
    </div>
</div>

<!-- Formulaire d'invitation -->
<div class="form-parr">
    <h3>🎁 Inviter un ami</h3>
    <p style="color:#666; font-size:13px; margin-bottom:18px;">
        Invitez un ami à rejoindre SkillSwap. Vous gagnerez <strong>+10 points</strong> et votre filleul <strong>+5 points</strong> dès son inscription.
    </p>
    <form method="POST" action="../../controllers/ParrainageC.php?action=inviter">
        <div class="input-row-parr">
            <input type="email" name="email_invite" placeholder="Email de votre ami..." required>
            <button type="submit" class="btn-inviter">📨 Envoyer l'invitation</button>
        </div>
    </form>
</div>

<!-- Tableau des invitations -->
<p class="section-title-parr">📋 Mes invitations</p>
<div class="table-parr">
    <table>
        <thead>
            <tr>
                <th>Email invité</th>
                <th>Code</th>
                <th>Statut</th>
                <th>Filleul</th>
                <th>Date invitation</th>
                <th>Date acceptation</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($invitations && count($invitations) > 0): ?>
                <?php foreach ($invitations as $inv): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inv['email_invite']); ?></td>
                        <td>
                            <span style="font-family: monospace; font-weight: bold; color: #7b2ff7; letter-spacing: 1px;">
                                <?php echo htmlspecialchars($inv['code_parrainage']); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $badgeClass = 'badge-attente';
                            $label = 'En attente';
                            if ($inv['statut'] === 'accepte') { $badgeClass = 'badge-accepte'; $label = 'Accepté ✅'; }
                            elseif ($inv['statut'] === 'expire') { $badgeClass = 'badge-expire'; $label = 'Annulé'; }
                            ?>
                            <span class="badge-parr <?php echo $badgeClass; ?>"><?php echo $label; ?></span>
                        </td>
                        <td>
                            <?php
                            if ($inv['nom_filleul']) {
                                echo htmlspecialchars($inv['prenom_filleul'] . ' ' . $inv['nom_filleul']);
                            } else {
                                echo '<span style="color:#94a3b8;">—</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($inv['date_invitation'])); ?></td>
                        <td>
                            <?php echo $inv['date_acceptation'] ? date('d/m/Y H:i', strtotime($inv['date_acceptation'])) : '<span style="color:#94a3b8;">—</span>'; ?>
                        </td>
                        <td>
                            <?php if ($inv['statut'] === 'en_attente'): ?>
                                <a href="../../controllers/ParrainageC.php?action=annuler&id=<?php echo $inv['id_parrainage']; ?>"
                                   class="btn-annuler-parr"
                                   onclick="return confirm('Annuler cette invitation ?')">🚫 Annuler</a>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:12px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="empty">Vous n'avez encore envoyé aucune invitation.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function copierCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert('Code copié : ' + code);
    });
}
</script>
