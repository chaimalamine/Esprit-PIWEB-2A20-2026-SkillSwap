<?php
require_once __DIR__ . '/../model/commentaire.php';
require_once __DIR__ . '/../config/config.php';

class commentaireC
{
    // ========== READ - Lister tous les commentaires ==========
    public function listCommentaires()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM commentaire ORDER BY datecom DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Commentaires par post ==========
    public function getCommentairesByPost($post_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM commentaire WHERE idpost = :idpost ORDER BY datecom ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':idpost' => $post_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Commentaire par ID ==========
    public function getCommentaireById($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM commentaire WHERE idcom = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== CREATE ==========
    public function createCommentaire($commentaire)
    {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO commentaire (contenu, datecom, idpost) 
                    VALUES (:contenu, CURDATE(), :idpost)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':contenu' => $commentaire->getContenu(),
                ':idpost' => $commentaire->getIdpost()
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
    public function updateCommentaire($id, $commentaire)
    {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE commentaire SET contenu = :contenu WHERE idcom = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':contenu' => $commentaire->getContenu(),
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
    public function deleteCommentaire($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "DELETE FROM commentaire WHERE idcom = :id";
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