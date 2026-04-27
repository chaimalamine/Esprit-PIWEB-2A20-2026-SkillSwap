<?php
// ============================================================
// Inclusion des dépendances nécessaires
// config.php = connexion à la base de données
// Competence.php = modèle (classe) Compétence
// ============================================================
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../models/Competence.php';

// ============================================================
// ROUTAGE DES ACTIONS - Gère les actions sur les compétences
// Ce bloc s'exécute AVANT la classe
// ============================================================
if (isset($_GET['action'])) {
    session_start();                              // Démarrer la session
    
    $competenceC = new CompetenceC();             // Instancier le contrôleur
    $action = $_GET['action'];                    // Récupérer l'action demandée
    
    // --- AJOUTER UNE COMPÉTENCE ---
    // Appelé depuis : ajouter_competence.php (ancien) ou CompetenceC.php?action=ajouter
    if ($action == 'ajouter' && isset($_POST['nom_competence'])) {
        $nom = $_POST['nom_competence'];           // Nom de la compétence
        $niveau = $_POST['niveau'];                // Niveau (Débutant/Intermédiaire/Expert)
        $categorie = $_POST['categorie'];          // Catégorie (Informatique/Design/etc.)
        $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;  // Heures (0 par défaut)
        $id_user = $_SESSION['user_id'];           // ID de l'utilisateur connecté
        
        // Vérifier que les champs obligatoires sont remplis
        if ($nom != '' && $niveau != '' && $categorie != '') {
            $competence = new Competence($nom, $niveau, $categorie, $heures, $id_user);  // Créer l'objet
            $competenceC->ajouterCompetence($competence);  // Appeler la fonction d'ajout
        }
        header('Location: ../views/backoffice/mes_competences.php');  // Rediriger vers la liste
        exit();                                   // Arrêter l'exécution
    }
    
    // --- MODIFIER UNE COMPÉTENCE ---
    // Appelé depuis : modifier_competence.php (ancien) ou CompetenceC.php?action=modifier
    if ($action == 'modifier' && isset($_POST['nom_competence'])) {
        $id = $_POST['id_competence'];             // ID de la compétence à modifier
        $nom = $_POST['nom_competence'];           // Nouveau nom
        $niveau = $_POST['niveau'];                // Nouveau niveau
        $categorie = $_POST['categorie'];          // Nouvelle catégorie
        $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;  // Nouvelles heures
        
        if ($nom != '' && $niveau != '' && $categorie != '') {
            $competence = new Competence($nom, $niveau, $categorie, $heures);  // Créer l'objet sans id_user
            $competenceC->modifierCompetence($competence, $id);  // Appeler la fonction de modification
        }
        header('Location: ../views/backoffice/mes_competences.php');  // Rediriger vers la liste
        exit();                                   // Arrêter l'exécution
    }
    
    // --- SUPPRIMER UNE COMPÉTENCE ---
    // Appelé depuis : mes_competences.php (clic sur 🗑️)
    if ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];                         // ID de la compétence à supprimer
        $competenceC->supprimerCompetence($id);    // Appeler la fonction de suppression
        header('Location: ../views/backoffice/mes_competences.php');  // Rediriger vers la liste
        exit();                                   // Arrêter l'exécution
    }
}

// ============================================================
// CLASSE COMPETENCEC - Contrôleur des opérations sur les compétences
// ============================================================
class CompetenceC {

    // --- AJOUTER une compétence dans la base de données ---
    // Appelée par : mes_competences.php (ajout multiple) et competences_front.php
    function ajouterCompetence($competence) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            INSERT INTO competence (nom_competence, niveau, categorie, heures_echangees, id_utilisateur) 
            VALUES (:nom, :niveau, :categorie, :heures, :id_user)
        ');
        $req->execute(array(
            'nom' => $competence->getNom_competence(),   // getter : nom
            'niveau' => $competence->getNiveau(),         // getter : niveau
            'categorie' => $competence->getCategorie(),   // getter : catégorie
            'heures' => $competence->getHeures_echangees(),  // getter : heures
            'id_user' => $competence->getId_utilisateur()    // getter : ID utilisateur
        ));
    }

    // --- MODIFIER une compétence existante ---
    // Appelée par : mes_competences.php (modification en ligne) et competences_front.php
    function modifierCompetence($competence, $id) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            UPDATE competence 
            SET nom_competence = :nom,             -- Nouveau nom
                niveau = :niveau,                  -- Nouveau niveau
                categorie = :categorie,            -- Nouvelle catégorie
                heures_echangees = :heures         -- Nouvelles heures
            WHERE id_competence = :id              -- Condition : la compétence à modifier
        ');
        $req->execute(array(
            'nom' => $competence->getNom_competence(),
            'niveau' => $competence->getNiveau(),
            'categorie' => $competence->getCategorie(),
            'heures' => $competence->getHeures_echangees(),
            'id' => $id
        ));
    }

    // --- SUPPRIMER une compétence par son ID ---
    // Appelée par : mes_competences.php et competences_front.php (clic sur 🗑️)
    function supprimerCompetence($id) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('DELETE FROM competence WHERE id_competence = :id');  // Requête SQL
        $req->execute(array('id' => $id));        // Exécuter avec l'ID
    }

    // --- RÉCUPÉRER toutes les compétences d'un utilisateur ---
    // Appelée par : mes_competences.php, competences_front.php
    // Utilisation : afficher le tableau des compétences de l'utilisateur connecté
    function getCompetencesByUser($id_utilisateur) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            SELECT * FROM competence 
            WHERE id_utilisateur = :id_user       
            ORDER BY date_ajout DESC              
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        return $req->fetchAll();                  // Retourner toutes les compétences
    }

    // --- CALCULER le total d'heures échangées par un utilisateur ---
    // Appelée par : frontdessign.php, mes_competences.php
    // Utilisation : afficher dans les stats "Heures échangées : Xh"
    function getTotalHeuresByUser($id_utilisateur) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            SELECT SUM(heures_echangees) as total -- Additionner toutes les heures
            FROM competence 
            WHERE id_utilisateur = :id_user       -- Pour cet utilisateur
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        $result = $req->fetch();
        return $result['total'] ? $result['total'] : 0;  // Retourner 0 si pas de compétences
    }

    // --- COMPTER le nombre de compétences d'un utilisateur ---
    // Appelée par : frontdessign.php, mes_competences.php
    // Utilisation : afficher "Compétences : X" dans les stats
    function countCompetencesByUser($id_utilisateur) {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            SELECT COUNT(*) as nb                 -- Compter le nombre
            FROM competence 
            WHERE id_utilisateur = :id_user       -- Pour cet utilisateur
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        $result = $req->fetch();
        return $result['nb'];                     // Retourner le nombre
    }

    // --- JOINTURE : Récupérer les compétences groupées par utilisateur ---
    // Appelée par : liste_users.php (colonne "Compétences" dans le tableau admin)
    // Utilisation : afficher toutes les compétences d'un utilisateur sur une seule ligne
    // Exemple de résultat : "Dev Web (Expert, 25h) | Design (Intermédiaire, 12h)"
    function getCompetencesGroupedByUser() {
        $db = config::getConnexion();             // Obtenir la connexion PDO
        $req = $db->prepare('
            SELECT u.id_utilisateur,              -- ID de l\'utilisateur
                   GROUP_CONCAT(                  -- Concaténer toutes les compétences
                       c.nom_competence, " (", c.niveau, ", ", c.heures_echangees, "h)" 
                       SEPARATOR " | "            -- Séparateur entre chaque compétence
                   ) AS competences
            FROM utilisateur u
            LEFT JOIN competence c ON u.id_utilisateur = c.id_utilisateur  -- Jointure
            GROUP BY u.id_utilisateur             -- Une ligne par utilisateur
            ORDER BY u.nom ASC                    -- Trier par nom
        ');
        $req->execute();
        return $req->fetchAll();                  // Retourner les résultats
    }

}
?>