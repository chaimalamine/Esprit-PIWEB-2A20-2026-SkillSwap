<?php

include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Ressource.php';



class RessourceController {

   

    // LISTE RESSOURCES avec recherche, filtre ET tri
    public function listeRessources($search = '', $type = '', $sort = '') {
        $db = config::getConnexion();
        try {
            $sql = 'SELECT * FROM ressource WHERE 1=1';
            if (!empty($search)) {
                $sql .= ' AND nom LIKE :search';
            }
            
            if (!empty($type)) {
                $sql .= ' AND type = :type';
            }
            

            $allowedSorts = ['nom', 'type', 'quantite_disponible', 'quantite_totale', 'etat', 'statut', 'date_achat', 'date_creation'];
            if (!empty($sort) && in_array($sort, $allowedSorts)) {
                $sql .= " ORDER BY $sort ASC";
            } else {
                $sql .= ' ORDER BY nom ASC'; // Tri par défaut
            }
            
            $stmt = $db->prepare($sql);
            
            // Bind des paramètres
            if (!empty($search)) {
                $stmt->bindValue(':search', '%' . $search . '%');
            }
            if (!empty($type)) {
                $stmt->bindValue(':type', $type);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('Erreur listeRessources: ' . $e->getMessage());
        }
    }

    // AJOUTER RESSOURCE
    public function addRessource($ressource) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('
                INSERT INTO ressource 
                (nom, type, description, quantite_disponible, quantite_totale, 
                 etat, id_proprietaire, date_achat, statut, date_creation) 
                VALUES 
                (:nom, :type, :description, :qte_disp, :qte_tot, 
                 :etat, :id_proprio, :date_achat, :statut, NOW())
            ');
            
            $req->execute([
                'nom' => $ressource->getNom(),
                'type' => $ressource->getType(),
                'description' => $ressource->getDescription(),
                'qte_disp' => $ressource->getQuantiteDisponible(),
                'qte_tot' => $ressource->getQuantiteTotale(),
                'etat' => $ressource->getEtat(),
                'id_proprio' => $ressource->getIdProprietaire(),
                'date_achat' => $ressource->getDateAchat(),
                'statut' => $ressource->getStatut()
            ]);
            
            return true;
        } catch (Exception $e) {
            die('Erreur addRessource: ' . $e->getMessage());
        }
    }

    // SUPPRIMER RESSOURCE
    public function deleteRessource($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM ressource WHERE id_ressource = :id');
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            die('Erreur deleteRessource: ' . $e->getMessage());
        }
    }

    // MODIFIER RESSOURCE
    public function updateRessource($ressource, $id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('
                UPDATE ressource 
                SET nom = :nom, type = :type, description = :description,
                    quantite_disponible = :qte_disp, quantite_totale = :qte_tot,
                    etat = :etat, date_achat = :date_achat, statut = :statut
                WHERE id_ressource = :id
            ');
            
            $req->execute([
                'id' => $id,
                'nom' => $ressource->getNom(),
                'type' => $ressource->getType(),
                'description' => $ressource->getDescription(),
                'qte_disp' => $ressource->getQuantiteDisponible(),
                'qte_tot' => $ressource->getQuantiteTotale(),
                'etat' => $ressource->getEtat(),
                'date_achat' => $ressource->getDateAchat(),
                'statut' => $ressource->getStatut()
            ]);
            
            return true;
        } catch (Exception $e) {
            die('Erreur updateRessource: ' . $e->getMessage());
        }
    }

    // RÉCUPÉRER RESSOURCE PAR ID
    public function getRessource($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM ressource WHERE id_ressource = :id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die('Erreur getRessource: ' . $e->getMessage());
        }
    }

    // OBTENIR LES TYPES UNIQUES (pour le filtre)
    public function getTypesRessources() {
        $db = config::getConnexion();
        try {
            $sql = 'SELECT DISTINCT type FROM ressource WHERE type IS NOT NULL ORDER BY type';
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            die('Erreur getTypesRessources: ' . $e->getMessage());
        }
    }

  
    public function afficherEvenementsParRessource($idRessource) {
        $db = config::getConnexion();
        try {
         
            $sql = "SELECT 
                        e.id_evenement,
                        e.titre,
                        e.description,
                        e.date_debut,
                        e.date_fin,
                        e.lieu,
                        e.capacite_max,
                        e.statut,
                        er.quantite_utilisee,
                        er.statut_reservation
                    FROM evenement e
                    INNER JOIN evenement_ressource er 
                        ON e.id_evenement = er.id_evenement
                    WHERE er.id_ressource = :idRessource
                    ORDER BY e.date_debut ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idRessource', $idRessource, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            die('Erreur afficherEvenementsParRessource: ' . $e->getMessage());
        }
    }


    public function listerRessources() {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id_ressource, nom, type FROM ressource ORDER BY nom ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('Erreur listerRessources: ' . $e->getMessage());
        }
    }

   
    public function searchEvenements() {
        
        $ressources = $this->listerRessources();
        
    
        $selectedId = $_POST['id_ressource'] ?? null;
        $evenements = [];

        if ($selectedId && is_numeric($selectedId)) {
            $evenements = $this->afficherEvenementsParRessource($selectedId);
        }
        
        require_once __DIR__ . '/../view/searchEvenements.php';
    }
}
?>