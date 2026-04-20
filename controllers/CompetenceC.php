<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../models/Competence.php';


// --- ROUTAGE DES ACTIONS (À METTRE TOUT EN HAUT) ---
if (isset($_GET['action'])) {
    session_start();

    
    $competenceC = new CompetenceC();
    $action = $_GET['action'];
    
    // AJOUTER UNE COMPÉTENCE
    if ($action == 'ajouter' && isset($_POST['nom_competence'])) {
        $nom = $_POST['nom_competence'];
        $niveau = $_POST['niveau'];
        $categorie = $_POST['categorie'];
        $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;
        $id_user = $_SESSION['user_id'];
        
        if ($nom != '' && $niveau != '' && $categorie != '') {
            $competence = new Competence($nom, $niveau, $categorie, $heures, $id_user);
            $competenceC->ajouterCompetence($competence);
        }
        header('Location: ../views/backoffice/mes_competences.php');
        exit();
    }
    
    // MODIFIER UNE COMPÉTENCE
    if ($action == 'modifier' && isset($_POST['nom_competence'])) {
        $id = $_POST['id_competence'];
        $nom = $_POST['nom_competence'];
        $niveau = $_POST['niveau'];
        $categorie = $_POST['categorie'];
        $heures = isset($_POST['heures_echangees']) ? $_POST['heures_echangees'] : 0;
        
        if ($nom != '' && $niveau != '' && $categorie != '') {
            $competence = new Competence($nom, $niveau, $categorie, $heures);
            $competenceC->modifierCompetence($competence, $id);
        }
        header('Location: ../views/backoffice/mes_competences.php');
        exit();
    }
    
    // SUPPRIMER UNE COMPÉTENCE
    if ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $competenceC->supprimerCompetence($id);
        header('Location: ../views/backoffice/mes_competences.php');
        exit();
    }
}


class CompetenceC {

    // Ajouter une compétence
    function ajouterCompetence($competence) {
        $db = config::getConnexion();
        $req = $db->prepare('
            INSERT INTO competence (nom_competence, niveau, categorie, heures_echangees, id_utilisateur) 
            VALUES (:nom, :niveau, :categorie, :heures, :id_user)
        ');
        $req->execute(array(
            'nom' => $competence->getNom_competence(),
            'niveau' => $competence->getNiveau(),
            'categorie' => $competence->getCategorie(),
            'heures' => $competence->getHeures_echangees(),
            'id_user' => $competence->getId_utilisateur()
        ));
    }

    // Modifier une compétence
    function modifierCompetence($competence, $id) {
        $db = config::getConnexion();
        $req = $db->prepare('
            UPDATE competence 
            SET nom_competence = :nom, 
                niveau = :niveau, 
                categorie = :categorie, 
                heures_echangees = :heures
            WHERE id_competence = :id
        ');
        $req->execute(array(
            'nom' => $competence->getNom_competence(),
            'niveau' => $competence->getNiveau(),
            'categorie' => $competence->getCategorie(),
            'heures' => $competence->getHeures_echangees(),
            'id' => $id
        ));
    }

    // Supprimer une compétence
    function supprimerCompetence($id) {
        $db = config::getConnexion();
        $req = $db->prepare('DELETE FROM competence WHERE id_competence = :id');
        $req->execute(array('id' => $id));
    }

    // Récupérer toutes les compétences d'un utilisateur
    function getCompetencesByUser($id_utilisateur) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT * FROM competence 
            WHERE id_utilisateur = :id_user 
            ORDER BY date_ajout DESC
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        return $req->fetchAll();
    }

    // Récupérer une compétence par son ID
    function getCompetenceById($id) {
        $db = config::getConnexion();
        $req = $db->prepare('SELECT * FROM competence WHERE id_competence = :id');
        $req->execute(array('id' => $id));
        return $req->fetch();
    }

    // Récupérer toutes les compétences (pour admin)
    function getAllCompetences() {
        $db = config::getConnexion();
        $req = $db->query('SELECT * FROM competence ORDER BY date_ajout DESC');
        return $req->fetchAll();
    }

    // Calculer le total d'heures échangées par un utilisateur
    function getTotalHeuresByUser($id_utilisateur) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT SUM(heures_echangees) as total 
            FROM competence 
            WHERE id_utilisateur = :id_user
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        $result = $req->fetch();
        return $result['total'] ? $result['total'] : 0;
    }

    // Compter le nombre de compétences d'un utilisateur
    function countCompetencesByUser($id_utilisateur) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT COUNT(*) as nb 
            FROM competence 
            WHERE id_utilisateur = :id_user
        ');
        $req->execute(array('id_user' => $id_utilisateur));
        $result = $req->fetch();
        return $result['nb'];
    }

    // Rechercher des compétences par nom
    function searchCompetences($search) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT * FROM competence 
            WHERE nom_competence LIKE :search 
            OR categorie LIKE :search
            ORDER BY nom_competence ASC
        ');
        $req->execute(array('search' => '%' . $search . '%'));
        return $req->fetchAll();
    }

    // Filtrer par catégorie
    function getCompetencesByCategorie($categorie) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT * FROM competence 
            WHERE categorie = :categorie 
            ORDER BY nom_competence ASC
        ');
        $req->execute(array('categorie' => $categorie));
        return $req->fetchAll();
    }

    // Récupérer les compétences populaires (les plus échangées)
    function getCompetencesPopulaires($limit = 4) {
        $db = config::getConnexion();
        $req = $db->prepare('
            SELECT nom_competence, categorie, SUM(heures_echangees) as total_heures, COUNT(*) as nb_utilisateurs
            FROM competence 
            GROUP BY nom_competence, categorie
            ORDER BY total_heures DESC
            LIMIT :limit
        ');
        $req->bindValue(':limit', $limit, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll();
    }

}
?>