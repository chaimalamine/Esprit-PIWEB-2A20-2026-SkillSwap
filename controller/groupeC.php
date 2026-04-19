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

    // ========== VALIDATION ==========
    public function validate($data)
    {
        $errors = [];
        
        if (empty(trim($data['nom']))) {
            $errors['nom'] = "Le nom du groupe est requis";
        } elseif (strlen(trim($data['nom'])) < 3) {
            $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
        } elseif (strlen(trim($data['nom'])) > 100) {
            $errors['nom'] = "Le nom ne peut pas dépasser 100 caractères";
        }
        
        if (empty(trim($data['description']))) {
            $errors['description'] = "La description est requise";
        } elseif (strlen(trim($data['description'])) < 10) {
            $errors['description'] = "La description doit contenir au moins 10 caractères";
        } elseif (strlen($data['description']) > 1000) {
            $errors['description'] = "La description ne peut pas dépasser 1000 caractères";
        }
        
        return $errors;
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