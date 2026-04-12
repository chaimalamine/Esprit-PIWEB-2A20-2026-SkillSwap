<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Event.php';

class EventController {

    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    // LISTE TOUS LES ÉVÉNEMENTS 
    public function listeEvents($search = '', $sort = '') {
        $db = config::getConnexion(); 
        try {
            $sql = 'SELECT * FROM evenement';
            
            // Recherche par titre
            if (!empty($search)) {
                $sql .= ' WHERE titre LIKE :search';
            }
            
            // Tri
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
            $req = $db->prepare('
                INSERT INTO evenement (titre, description, date_debut, date_fin, lieu, capacite_max, places_restantes, id_organisateur, statut, date_creation, date_modification) 
                VALUES (:titre, :description, :date_debut, :date_fin, :lieu, :capacite_max, :places_restantes, :id_organisateur, :statut, NOW(), NULL)
            ');
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

    //  MODIFIER ÉVÉNEMENT 
    public function updateEvent($event, $id) {
        $db = config::getConnexion(); 
        try {
            $req = $db->prepare('
                UPDATE evenement 
                SET titre = :titre, description = :description, 
                    date_debut = :date_debut, date_fin = :date_fin, 
                    lieu = :lieu, capacite_max = :capacite_max, 
                    places_restantes = :places_restantes, 
                    statut = :statut, date_modification = NOW()
                WHERE id_evenement = :id
            ');
            $req->execute([
                'id' => $id,
                'titre' => $event->getTitre(),
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

    //RÉCUPÉRER UN ÉVÉNEMENT PAR ID 
    public function getEvent($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM evenement WHERE id_evenement = :id'); 
            $req->execute(['id' => $id]); 
            return $req->fetch();
        } catch(Exception $e){
            die('Erreur getEvent: ' . $e->getMessage());
        }
    }
}
?>