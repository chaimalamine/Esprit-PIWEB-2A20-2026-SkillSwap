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

    // ========== VALIDATION ==========
    public function validate($data)
    {
        $errors = [];
        
        if (empty(trim($data['titre']))) {
            $errors['titre'] = "Le titre est requis";
        } elseif (strlen(trim($data['titre'])) < 3) {
            $errors['titre'] = "Le titre doit contenir au moins 3 caractères";
        } elseif (strlen(trim($data['titre'])) > 200) {
            $errors['titre'] = "Le titre ne peut pas dépasser 200 caractères";
        }
        
        if (empty(trim($data['contenu']))) {
            $errors['contenu'] = "Le contenu est requis";
        } elseif (strlen(trim($data['contenu'])) < 10) {
            $errors['contenu'] = "Le contenu doit contenir au moins 10 caractères";
        } elseif (strlen(trim($data['contenu'])) > 2000) {
            $errors['contenu'] = "Le contenu ne peut pas dépasser 2000 caractères";
        }
        
        return $errors;
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