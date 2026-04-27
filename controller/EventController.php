<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Event.php';


class EventController {

    // liste tout les evenements  et tri rech 
    public function listeEvents($search = '', $sort = '') {
        $db = config::getConnexion(); 
        try {
            $sql = 'SELECT * FROM evenement';
            if (!empty($search)) {
                 $sql .= ' WHERE titre LIKE :search'; 
                 }
            if (!empty($sort)) {
                $allowed = ['titre', 'date_debut', 'lieu', 'capacite_max'];
                if (in_array($sort, $allowed)) {
                     $sql .= ' ORDER BY ' . $sort . ' ASC';
                      }
            } else {
                 $sql .= ' ORDER BY date_debut DESC'; 
                 }
            $stmt = $db->prepare($sql);
            if (!empty($search)) {
                 $stmt->bindValue(':search', '%' . $search . '%');
                  }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
             die('Erreur listeEvents: ' . $e->getMessage()); 
             }
    }

    // AJOUTER ÉVÉNEMENT
    public function addEvent($event) {
        $db = config::getConnexion(); 
        try {
            $req = $db->prepare('INSERT INTO evenement (titre, description, date_debut, date_fin, lieu, capacite_max, places_restantes, id_organisateur, statut, date_creation, date_modification) VALUES (:titre, :description, :date_debut, :date_fin, :lieu, :capacite_max, :places_restantes, :id_organisateur, :statut, NOW(), NULL)');
            $req->execute([
                'titre' => $event->getTitre(),
                 'description' => $event->getDescription(),
                'date_debut' => $event->getDateDebut(), 
                'date_fin' => $event->getDateFin(),
                'lieu' => $event->getLieu(), 
                'capacite_max' => $event->getCapaciteMax(),
                'places_restantes' => $event->getPlacesRestantes(),
                'id_organisateur' => $event->getIdOrganisateur(), 
                'statut' => $event->getStatut()
            ]);
            return true;
        } catch (Exception $e) {
             die('Erreur addEvent: ' . $e->getMessage());
              }
    }

    // MODIFIER ÉVÉNEMENT 
    public function updateEvent($event, $id) {
        $db = config::getConnexion(); 
        try {
            $req = $db->prepare('UPDATE evenement SET titre = :titre, description = :description, date_debut = :date_debut, date_fin = :date_fin, lieu = :lieu, capacite_max = :capacite_max, places_restantes = :places_restantes, statut = :statut, date_modification = NOW() WHERE id_evenement = :id');
            $req->execute([
                'id' => $id, 'titre' => $event->getTitre(), 
                'description' => $event->getDescription(),
                'date_debut' => $event->getDateDebut(),
                 'date_fin' => $event->getDateFin(),
                'lieu' => $event->getLieu(), 
                'capacite_max' => $event->getCapaciteMax(),
                'places_restantes' => $event->getPlacesRestantes(),
                 'statut' => $event->getStatut()
            ]);
            return true;
        } catch (Exception $e) { 
            die('Erreur updateEvent: ' . $e->getMessage()); 
            }
    }

    // SUPPRIMER ÉVÉNEMENT
    public function deleteEvent($id) {
        $db = config::getConnexion(); 
        try {
            $req = $db->prepare('DELETE FROM evenement WHERE id_evenement = :id');
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
             die('Erreur deleteEvent: ' . $e->getMessage());
              }
    }

    // RÉCUPÉRER UN ÉVÉNEMENT PAR ID
    public function getEvent($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM evenement WHERE id_evenement = :id'); 
            $req->execute(['id' => $id]); 
            return $req->fetch();
        } catch(Exception $e){ die('Erreur getEvent: ' . $e->getMessage()); }
    }

   
    // JOINTURE 
  

    public function afficherRessourcesParEvenement($idEvenement) {
        $db = config::getConnexion();
        try {
            $sql = "SELECT r.id_ressource, r.nom, r.type, r.etat, r.quantite_disponible, er.quantite_utilisee, er.statut_reservation FROM ressource r INNER JOIN evenement_ressource er ON r.id_ressource = er.id_ressource WHERE er.id_evenement = :idEvent ORDER BY r.nom ASC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idEvent', $idEvenement, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) { die('Erreur afficherRessourcesParEvenement: ' . $e->getMessage()); }
    }

    public function listerEvenements() {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id_evenement, titre FROM evenement ORDER BY titre ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) { die('Erreur listerEvenements: ' . $e->getMessage()); }
    }

  /*  public function searchRessources() {
        $evenements = $this->listerEvenements();
        $selectedId = $_POST['id_evenement'] ?? null;
        $ressources = [];
        if ($selectedId && is_numeric($selectedId)) {
            $ressources = $this->afficherRessourcesParEvenement($selectedId);
        }
        
    }*/

    public function searchRessourcesFront() {
        $evenements = $this->listerEvenementsPublics();
        $selectedId = $_POST['id_evenement'] ?? null;
        $ressources = [];
        if ($selectedId && is_numeric($selectedId)) {
            $ressources = $this->afficherRessourcesParEvenement($selectedId);
        }
        //require_once __DIR__ . '/../view/searchRessourcesFront.php';
    }

    public function listerEvenementsPublics() {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id_evenement, titre, date_debut FROM evenement WHERE statut = 'Actif' AND date_debut >= NOW() ORDER BY date_debut ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function associerRessource() {
        $idEvenement = $_POST['id_evenement'] ?? null;
        $idRessource = $_POST['id_ressource'] ?? null;
        $quantite = intval($_POST['quantite_utilisee'] ?? 1);
        $statut = $_POST['statut_reservation'] ?? 'En attente';
        if ($idEvenement && $idRessource) {
            $db = config::getConnexion();
            try {
                $req = $db->prepare('INSERT INTO evenement_ressource (id_evenement, id_ressource, quantite_utilisee, statut_reservation) VALUES (:idEvent, :idRess, :quantite, :statut) ON DUPLICATE KEY UPDATE quantite_utilisee = :quantite, statut_reservation = :statut');
                $req->execute(['idEvent' => $idEvenement, 'idRess' => $idRessource, 'quantite' => $quantite, 'statut' => $statut]);
                header('Location: index.php?action=gererRessourcesEvenement&id=' . $idEvenement . '&msg=associated');
                exit();
            } catch (Exception $e) { die('Erreur associerRessource: ' . $e->getMessage()); }
        }
    }

    public function dissocierRessource() {
        $idEvenement = $_GET['id_evenement'] ?? null;
        $idRessource = $_GET['id_ressource'] ?? null;
        if ($idEvenement && $idRessource) {
            $db = config::getConnexion();
            try {
                $req = $db->prepare('DELETE FROM evenement_ressource WHERE id_evenement = :idEvent AND id_ressource = :idRess');
                $req->execute(['idEvent' => $idEvenement, 'idRess' => $idRessource]);
                header('Location: index.php?action=gererRessourcesEvenement&id=' . $idEvenement . '&msg=dissociated');
                exit();
            } catch (Exception $e) { die('Erreur dissocierRessource: ' . $e->getMessage()); }
        }
    }

  
    public function gererRessourcesEvenement() {
        $idEvenement = $_GET['id'] ?? null;
        if (!$idEvenement) { 
            header('Location: dashboard.php?tab=events');
             exit();
              }
        
        // Récupérer les infos de l'événement
        $event = $this->getEvent($idEvenement);
        
        $ressourceController = new RessourceController();
        $toutesRessources = $ressourceController->listeRessources();
         if (!$event) {
        header('Location: dashboard.php?tab=events&error=event_not_found');
        exit();
    }

        // Récupérer les ress dejas associe a event 
        $db = config::getConnexion();
        try {
            $sql = "SELECT er.id_ressource, er.quantite_utilisee, er.statut_reservation, r.nom, r.type, r.quantite_disponible 
            FROM evenement_ressource er
             INNER JOIN ressource r ON er.id_ressource = r.id_ressource
              WHERE er.id_evenement = :idEvent";
            $stmt = $db->prepare($sql);
            $stmt->execute(['idEvent' => $idEvenement]);
            $ressourcesAssociees = $stmt->fetchAll();
        } catch (Exception $e) { 
            die('Erreur: ' . $e->getMessage()); 
            }
        
       
    }
}
?>