<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Ressource.php';

class RessourceController {

    private $ressourceModel;

    public function __construct() {
        $this->ressourceModel = new Ressource();
    }

    // LISTE TOUTES LES RESSOURCES
    public function listeRessources($search = '', $type = '') {
        $db = config::getConnexion();
        try {
            $sql = 'SELECT * FROM ressource WHERE 1=1';
            
            if (!empty($search)) {
                $sql .= ' AND nom LIKE :search';
            }
            if (!empty($type)) {
                $sql .= ' AND type = :type';
            }
            
            $sql .= ' ORDER BY nom ASC';
            
            $stmt = $db->prepare($sql);
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
}
?>