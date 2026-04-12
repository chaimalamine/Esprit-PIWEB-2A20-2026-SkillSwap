<?php
include_once 'config.php';
include_once 'model/Ressource.php';
include_once 'controller/RessourceController.php';

session_start();
$controller = new RessourceController();

$errors = [];
$success = '';

// GESTION DE L'AJOUT (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ressource'])) {
    
    $nom = trim($_POST['nom'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $quantite_disponible = intval($_POST['quantite_disponible'] ?? 0);
    $quantite_totale = intval($_POST['quantite_totale'] ?? 0);
    $etat = trim($_POST['etat'] ?? '');
    $date_achat = $_POST['date_achat'] ?? null;
    $statut = trim($_POST['statut'] ?? 'Disponible');
    $id_proprietaire = 1; // À remplacer par $_SESSION['user_id']

    
    if (empty($nom)) $errors[] = "Le nom est obligatoire.";
    if ($quantite_disponible < 0) $errors[] = "La quantité disponible ne peut pas être négative.";
    if ($quantite_totale < 0) $errors[] = "La quantité totale ne peut pas être négative.";
    if ($quantite_disponible > $quantite_totale) {
        $errors[] = "La quantité disponible ne peut pas dépasser la quantité totale.";
    }

    if (empty($errors)) {
        $newRessource = new Ressource(
            $nom, $type, $description, $quantite_disponible, $quantite_totale,
            $etat, $id_proprietaire, $date_achat, $statut
        );
        
        $controller->addRessource($newRessource);
        header('Location: ressources.php?msg=created');
        exit();
    }
}

// GESTION DE LA SUPPRESSION
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->deleteRessource($_GET['id']);
    header('Location: ressources.php?msg=deleted');
    exit();
}

// PARAMÈTRES DE RECHERCHE ET FILTRE
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';

// RÉCUPÉRATION DE LA LISTE
$ressources = $controller->listeRessources($search, $type_filter);
$types = $controller->getTypesRessources();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - Gestion des Ressources</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: 'Segoe UI', sans-serif; 
            background: #f0f4f8; 
        }
        .sidebar { 
            width: 220px; 
            background: linear-gradient(180deg, #6a11cb, #2575fc); 
            color: white; 
            height: 100vh; 
            padding: 20px; 
            position: fixed;
            left: 0;
            top: 0;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { 
            display: block; 
            color: white; 
            text-decoration: none; 
            margin: 12px 0; 
            padding: 12px 15px; 
            border-radius: 8px; 
            transition: 0.3s; 
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
        .main { 
            margin-left: 220px; 
            padding: 25px; 
            min-height: 100vh;
        }
        .topbar { 
            background: white; 
            padding: 15px 20px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            margin-bottom: 25px;
        }
        .topbar h1 { margin: 0; color: #333; font-size: 24px; }
        .card { 
            background: white; 
            padding: 25px; 
            border-radius: 15px; 
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
            margin-bottom: 25px; 
        }
        .form-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px; 
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: 600; 
            color: #444; 
            font-size: 14px;
        }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px;
        }
        .form-group input:focus, .form-group textarea:focus { 
            border-color: #7b2ff7; 
            outline: none; 
        }
        .btn { 
            background: #2575fc; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 15px; 
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover { background: #1a5bb5; }
        .btn-delete { background: #ff4b4b; padding: 6px 12px; font-size: 13px; }
        .btn-delete:hover { background: #e03e3e; }
        .btn-edit { background: #ffc107; color: #333; padding: 6px 12px; font-size: 13px; margin-right: 6px; }
        .btn-edit:hover { background: #e0a800; }
        .search-filter { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 20px; 
            flex-wrap: wrap;
        }
        .search-filter input, .search-filter select { 
            padding: 8px 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th, td { 
            padding: 14px 12px; 
            text-align: left; 
            border-bottom: 1px solid #eee; 
        }
        th { 
            background: #f8f9fa; 
            color: #555; 
            font-weight: 600; 
        }
        .badge { 
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .success-msg { 
            color: #155724; 
            background: #d4edda; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SkillSwap</h2>
    <a href="dashboard.php">📊 Mes événements</a>
    <a href="ressources.php" class="active">🛠️ Mes Ressources</a>
    <a href="#">📢 Offres</a>
    <a href="index.php">🏠 Retour au Site</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>🛠️ Gestion des Ressources</h1>
    </div>

    <!-- MESSAGES -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul style="margin:0; padding-left:20px;">
                <?php foreach($errors as $err) echo "<li>" . htmlspecialchars($err) . "</li>"; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="success-msg">
            <?php
            if ($_GET['msg'] === 'created') echo "✅ Ressource ajoutée avec succès !";
            elseif ($_GET['msg'] === 'deleted') echo "🗑️ Ressource supprimée avec succès !";
            ?>
        </div>
    <?php endif; ?>

    <!-- FORMULAIRE D'AJOUT -->
    <div class="card">
        <h2 style="margin-top:0; color:#7b2ff7;">➕ Ajouter une ressource</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom de la ressource *</label>
                    <input type="text" name="nom" placeholder="Ex: Projecteur, Salle A..." required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="">Sélectionner...</option>
                        <option value="Matériel">Matériel</option>
                        <option value="Salle">Salle</option>
                        <option value="Équipement">Équipement</option>
                        <option value="Consommable">Consommable</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantité disponible *</label>
                    <input type="number" name="quantite_disponible" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label>Quantité totale *</label>
                    <input type="number" name="quantite_totale" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label>État</label>
                    <select name="etat">
                        <option value="">Sélectionner...</option>
                        <option value="Neuf">Neuf</option>
                        <option value="Bon état">Bon état</option>
                        <option value="Usé">Usé</option>
                        <option value="À réparer">À réparer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="Disponible">Disponible</option>
                        <option value="Réservé">Réservé</option>
                        <option value="Indisponible">Indisponible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date d'achat</label>
                    <input type="date" name="date_achat">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Description détaillée..."></textarea>
            </div>
            <button type="submit" name="add_ressource" class="btn">💾 Enregistrer la ressource</button>
        </form>
    </div>

    <!-- LISTE DES RESSOURCES -->
    <div class="card">
        <h2 style="margin-top:0; color:#7b2ff7;">📋 Mes Ressources</h2>
        
        <!-- Recherche et Filtre -->
        <form method="GET" class="search-filter">
            <input type="text" name="search" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($search) ?>">
            <select name="type">
                <option value="">Tous les types</option>
                <?php foreach($types as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= $type_filter === $t ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn" style="padding: 8px 16px;">🔍 Filtrer</button>
            <?php if (!empty($search) || !empty($type_filter)): ?>
                <a href="ressources.php" class="btn" style="background:#6c757d; padding: 8px 16px;">✕ Reset</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>État</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ressources)): ?>
                    <?php foreach ($ressources as $r): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($r['nom']) ?></strong><br>
                            <small style="color:#888;"><?= htmlspecialchars($r['description'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($r['type'] ?? '-') ?></td>
                        <td>
                            <?= (int)$r['quantite_disponible'] ?> / <?= (int)$r['quantite_totale'] ?>
                            <?php if ($r['quantite_disponible'] == 0): ?>
                                <span class="badge badge-danger">Épuisé</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['etat'] ?? '-') ?></td>
                        <td>
                            <?php
                            $badgeClass = 'badge-success';
                            if ($r['statut'] === 'Réservé') $badgeClass = 'badge-warning';
                            elseif ($r['statut'] === 'Indisponible') $badgeClass = 'badge-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($r['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-edit">✏️ Modifier</a>
                            <a href="?action=delete&id=<?= $r['id_ressource'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Supprimer cette ressource ?');">
                               🗑️ Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#888; padding:30px;">
                            <?= !empty($search) ? 'Aucune ressource trouvée.' : 'Aucune ressource enregistrée.' ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>