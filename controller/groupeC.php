<?php
require_once __DIR__ . '/../model/groupe.php';
require_once __DIR__ . '/../config/config.php';

class groupeC
{
    // ========== READ - Lister tous les groupes ==========
    public function listGroupes()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM groupe ORDER BY datecreation DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Groupe par ID ==========
    public function getGroupeById($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM groupe WHERE idgroup = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Rechercher des groupes par nom ==========
    public function searchGroupes($keyword)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM groupe WHERE nom LIKE :keyword ORDER BY datecreation DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':keyword' => '%' . $keyword . '%']);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== CREATE ==========
    public function createGroupe($groupe)
    {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO groupe (nom, description, datecreation) 
                    VALUES (:nom, :description, CURDATE())";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':nom' => $groupe->getNom(),
                ':description' => $groupe->getDescription()
            ]);
            
            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                die("Erreur SQL: " . $error[2]);
            }
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== UPDATE ==========
    public function updateGroupe($id, $groupe)
    {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE groupe SET nom = :nom, description = :description 
                    WHERE idgroup = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':nom' => $groupe->getNom(),
                ':description' => $groupe->getDescription(),
                ':id' => $id
            ]);
            
            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                die("Erreur SQL: " . $error[2]);
            }
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== DELETE ==========
    public function deleteGroupe($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "DELETE FROM groupe WHERE idgroup = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);
            
            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                die("Erreur SQL: " . $error[2]);
            }
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }
}
?>