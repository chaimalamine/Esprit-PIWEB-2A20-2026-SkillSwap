<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap - Événements</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; color: #dbb3d5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #6a11cb; color: white; }
        .btn { padding: 8px 15px; background: #2575fc; color: white; border: none; cursor: pointer; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .btn-delete { background: #ff4b4b; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box; }
        .form-box { background: #e9ecef; padding: 20px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h1>SkillSwap - Gestion des Événements</h1>

    <h2>Liste des événements</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Lieu</th>
            <th>Date</th>
            <th>Action</th> 
        </tr>
        <?php 
        // transforme les données brutes venant de la base de données 
        // (via $events) en lignes HTML lisibles dans un tableau, avec pour chaque événement ses informations
        //  et un bouton de suppression sécurisé, ou affiche un message d'absence si la liste est vide.

        if (!empty($events)) {
            foreach ($events as $e) {
                echo '<tr>';
                echo '<td>' . $e->getId() . '</td>';
                echo '<td>' . htmlspecialchars($e->getTitle()) . '</td>';
                echo '<td>' . htmlspecialchars($e->getDescription()) . '</td>';
                echo '<td>' . htmlspecialchars($e->getLocation()) . '</td>';
                echo '<td>' . $e->getDate() . '</td>';
                // Bouton Supp
                echo '<td><a href="index.php?action=delete&id=' . $e->getId() . '" class="btn btn-delete" onclick="return confirm(\'Voulez-vous vraiment supprimer cet événement ?\');">Supprimer</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6" style="text-align:center;">Aucun événement.</td></tr>';
        }
        ?>
    </table>

    <!-- Formulaire ajout -->

    <div class="form-box">
        <h2>Ajouter un événement</h2>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Titre de l'événement" >
            <textarea name="description" placeholder="Description" rows="3" ></textarea>
            <input type="text" name="location" placeholder="Lieu" >
            <input type="datetime-local" name="date" >
            <button type="submit" class="btn">Publier</button>
        </form>
    </div>
</div>
</body>
</html>