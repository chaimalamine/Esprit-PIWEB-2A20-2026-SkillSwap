<?php

include_once __DIR__ . '/../config.php';

class CoursC {

    public function listeCours() {
        $db = config::getConnexion();
        try {
            $liste = $db->query('SELECT * FROM cours');
            return $liste;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
            
        }
    }

    public function addCours($cours) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('INSERT INTO cours (titre, description) VALUES (:t,:d)');
            $req->execute([
                't' => $cours->getTitre(),
                'd' => $cours->getDescription()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteCours($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM cours WHERE id=:id');
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateCours($cours, $id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE cours SET titre=:t, description=:d WHERE id=:id');
            $req->execute([
                'id' => $id,
                't'  => $cours->getTitre(),
                'd'  => $cours->getDescription()
            ]);
        } catch (Exception $e) {
            die('Erreur update: ' . $e->getMessage());
        }
    }

    public function getCours($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM cours WHERE id=:id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die('Erreur getCours: ' . $e->getMessage());
        }
    }
}
