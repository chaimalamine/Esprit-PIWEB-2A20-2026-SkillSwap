<?php

require_once __DIR__ . '/../config.php';

$idEvenement = $idEvenement ?? ($_GET['id'] ?? null);
$toutesRessources = $toutesRessources ?? [];
$ressourcesAssociees = $ressourcesAssociees ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les ressources - <?= htmlspecialchars($event['titre'] ?? 'Événement') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; padding: 40px 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #7b2ff7; margin-bottom: 10px; }
        .event-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #7b2ff7; }
        .section { margin-bottom: 40px; }
        .section-title { color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #7b2ff7; }
        .assoc-form { background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 30px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-group select, .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: #7b2ff7; color: white; }
        .btn-primary:hover { background: #5a1db8; }
        .btn-danger { background: #ff4b4b; color: white; }
        .btn-danger:hover { background: #e03e3e; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #e0e0e0; text-align: left; }
        th { background: #7b2ff7; color: white; font-weight: 600; }
        tr:nth-child(even) { background: #f8f9fa; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-confirm { background: #d4edda; color: #155724; }
        .badge-attente { background: #fff3cd; color: #856404; }
        .back-link { display: inline-block; margin-top: 20px; color: #7b2ff7; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .empty { text-align: center; color: #888; padding: 30px; background: #f8f9fa; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔗 Gérer les ressources de l'événement</h2>
        
        <div class="event-info">
            <strong>Événement :</strong> <?= htmlspecialchars($event['titre'] ?? 'N/A') ?><br>
            <strong>Date :</strong> <?= !empty($event['date_debut']) ? date('d/m/Y H:i', strtotime($event['date_debut'])) : 'Non définie' ?><br>
            <strong>Lieu :</strong> <?= htmlspecialchars($event['lieu'] ?? 'N/A') ?>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'associated'): ?>
                <div class="alert alert-success">✅ Ressource associée avec succès !</div>
            <?php elseif ($_GET['msg'] === 'dissociated'): ?>
                <div class="alert alert-success">✅ Ressource dissociée avec succès !</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Formulaire pour associer une nouvelle ressource -->
        <div class="section">
            <h3 class="section-title">➕ Associer une nouvelle ressource</h3>
            <form method="POST" action="index.php?action=associerRessource" class="assoc-form">
                <input type="hidden" name="id_evenement" value="<?= htmlspecialchars($idEvenement) ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_ressource">Ressource :</label>
                        <select name="id_ressource" id="id_ressource" required>
                            <option value="">-- Sélectionner une ressource --</option>
                            <?php if (!empty($toutesRessources)): ?>
                                <?php foreach ($toutesRessources as $r): 
                                    $dejaAssociee = false;
                                    if (!empty($ressourcesAssociees)) {
                                        foreach ($ressourcesAssociees as $ra) {
                                            if (isset($ra['id_ressource'], $r['id_ressource']) && $ra['id_ressource'] == $r['id_ressource']) {
                                                $dejaAssociee = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($dejaAssociee) continue;
                                ?>
                                    <option value="<?= $r['id_ressource'] ?>">
                                        <?= htmlspecialchars($r['nom']) ?> (<?= htmlspecialchars($r['type'] ?? '') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Aucune ressource disponible</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantite_utilisee">Quantité utilisée :</label>
                        <input type="number" name="quantite_utilisee" id="quantite_utilisee" min="1" value="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="statut_reservation">Statut :</label>
                        <select name="statut_reservation" id="statut_reservation">
                            <option value="En attente">En attente</option>
                            <option value="Confirmé">Confirmé</option>
                            <option value="Refusé">Refusé</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">✅ Associer la ressource</button>
            </form>
        </div>

        <!-- Liste des ressources déjà associées -->
        <div class="section">
            <h3 class="section-title">📋 Ressources associées (<?= count($ressourcesAssociees) ?>)</h3>
            
            <?php if (!empty($ressourcesAssociees)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Qté disponible</th>
                            <th>Qté utilisée</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ressourcesAssociees as $ra): 
                            $badgeClass = (isset($ra['statut_reservation']) && $ra['statut_reservation'] === 'Confirmé') ? 'badge-confirm' : 'badge-attente';
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ra['nom'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($ra['type'] ?? '-') ?></td>
                            <td><?= isset($ra['quantite_disponible']) ? (int)$ra['quantite_disponible'] : 0 ?></td>
                            <td><?= isset($ra['quantite_utilisee']) ? (int)$ra['quantite_utilisee'] : 0 ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($ra['statut_reservation'] ?? 'N/A') ?></span></td>
                            <td>
                                <a href="index.php?action=dissocierRessource&id_evenement=<?= $idEvenement ?>&id_ressource=<?= $ra['id_ressource'] ?? '' ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Dissocier cette ressource de l\'événement ?');">
                                   🗑️ Dissocier
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">
                    <p>Aucune ressource n'est actuellement associée à cet événement.</p>
                    <p style="margin-top: 10px; font-size: 14px;">Utilisez le formulaire ci-dessus pour ajouter des ressources.</p>
                </div>
            <?php endif; ?>
        </div>

        <a href="dashboard.php?tab=events" class="back-link">← Retour aux événements</a>
    </div>
</body>
</html>