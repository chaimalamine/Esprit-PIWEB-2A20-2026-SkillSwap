<?php
require_once __DIR__ . '/../model/post.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/groupeC.php';

class postC
{
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

    public function getLastInsertId()
    {
        $db = config::getConnexion();
        return $db->lastInsertId();
    }

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