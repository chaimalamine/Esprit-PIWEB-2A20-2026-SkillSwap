<?php

require_once __DIR__ . '/../config.php';

// Init variables éviter les warnings
$selectedId = $selectedId ?? null;
$evenements = $evenements ?? [];
$ressources = $ressources ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ressources par Événement - SkillSwap</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h2 { 
            color: #7b2ff7; 
            margin-bottom: 30px;
            font-size: 28px;
        }
        .form-group { 
            margin-bottom: 30px; 
            display: flex; 
            gap: 15px; 
            align-items: center; 
            flex-wrap: wrap;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }
        select { 
            padding: 12px 15px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            flex: 1; 
            min-width: 300px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        select:focus {
            outline: none;
            border-color: #7b2ff7;
        }
        button { 
            padding: 12px 25px; 
            background: #7b2ff7; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
        }
        button:hover { 
            background: #5a1db8; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 30px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td { 
            padding: 15px; 
            border: 1px solid #e0e0e0; 
            text-align: left; 
        }
        th { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) { 
            background: #f8f9fa; 
        }
        tr:hover {
            background: #f0f0f0;
        }
        .badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600;
            display: inline-block;
        }
        .badge-confirm { 
            background: #d4edda; 
            color: #155724; 
        }
        .badge-attente { 
            background: #fff3cd; 
            color: #856404; 
        }
        .badge-refuse { 
            background: #f8d7da; 
            color: #721c24; 
        }
        .empty { 
            text-align: center; 
            color: #888; 
            padding: 40px; 
            font-size: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
        .back-link { 
            display: inline-block; 
            margin-top: 30px; 
            color: #7b2ff7; 
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .back-link:hover { 
            color: #5a1db8;
            text-decoration: underline;
        }
        .result-title {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 Ressources associées à un événement</h2>
        
        <!-- Formulaire pour sélectionner un événement -->
        <form method="POST">
            <div class="form-group">
                <label for="id_evenement"><strong>Choisir un événement :</strong></label>
                <select name="id_evenement" id="id_evenement" >
                    <option value="">-- Sélectionner un événement --</option>
                    <?php 
                    if (!empty($evenements)):
                        foreach ($evenements as $e): 
                    ?>
                        <option value="<?= htmlspecialchars($e['id_evenement']) ?>" 
                            <?= ($selectedId == $e['id_evenement']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['titre']) ?>
                        </option>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <option value="" disabled>Aucun événement disponible</option>
                    <?php endif; ?>
                </select>
                <button type="submit">Rechercher</button>
            </div>
        </form>

        <!-- Afficher les ressources correspondantes -->
        <?php if ($selectedId && !empty($ressources)): ?>
            <h3 class="result-title">📋 Ressources trouvées :</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nom de la ressource</th>
                        <th>Type</th>
                        <th>Qté disponible</th>
                        <th>Qté utilisée</th>
                        <th>État</th>
                        <th>Statut réservation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ressources as $r): 
                        $badgeClass = match($r['statut_reservation']) {
                            'Confirmé' => 'badge-confirm',
                            'Refusé' => 'badge-refuse',
                            default => 'badge-attente'
                        };
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($r['type'] ?? '-') ?></td>
                        <td><?= (int)$r['quantite_disponible'] ?></td>
                        <td><?= (int)$r['quantite_utilisee'] ?></td>
                        <td><?= htmlspecialchars($r['etat'] ?? '-') ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($r['statut_reservation']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($selectedId): ?>
            <div class="empty">
                ⚠️ Aucune ressource associée à cet événement.
            </div>
        <?php else: ?>
            <div class="empty">
                📌 Sélectionnez un événement pour voir ses ressources.
            </div>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Retour au dashboard</a>
    </div>
</body>
</html>