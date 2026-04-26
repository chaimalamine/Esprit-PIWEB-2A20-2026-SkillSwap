<?php
require_once __DIR__ . '/../model/post.php';
require_once __DIR__ . '/../config/config.php';

class postC
{
    // ========== READ - Lister tous les posts ==========
    public function listPosts()
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM post ORDER BY datepost DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Posts par groupe ==========
    public function getPostsByGroup($groupe_id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM post WHERE idgroup = :idgroup ORDER BY datepost DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([':idgroup' => $groupe_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== READ - Post par ID ==========
    public function getPostById($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "SELECT * FROM post WHERE idpost = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            die("Erreur: " . $e->getMessage());
        }
    }

    // ========== CREATE ==========
    public function createPost($post)
    {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO post (titre, contenu, datepost, idgroup) 
                    VALUES (:titre, :contenu, NOW(), :idgroup)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':titre' => $post->getTitre(),
                ':contenu' => $post->getContenu(),
                ':idgroup' => $post->getIdgroup()
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
    public function updatePost($id, $post)
    {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE post SET titre = :titre, contenu = :contenu WHERE idpost = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':titre' => $post->getTitre(),
                ':contenu' => $post->getContenu(),
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
    public function deletePost($id)
    {
        $db = config::getConnexion();
        try {
            $sql = "DELETE FROM post WHERE idpost = :id";
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