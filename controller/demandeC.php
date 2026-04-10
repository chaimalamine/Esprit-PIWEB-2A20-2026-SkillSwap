<?php

include_once __DIR__ . '/../config.php';

class DemandeC {

    public function listeDemandes() {
        $db = config::getConnexion();
        try {
            $liste = $db->query('SELECT * FROM demande');
            return $liste;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
            
        }
    }

    public function addDemande($demande) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('INSERT INTO demande (titre, description) VALUES (:t,:d)');
            $req->execute([
                't' => $demande->getTitre(),
                'd' => $demande->getDescription()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteDemande($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM demande WHERE id=:id');
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateDemande($demande, $id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE demande SET titre=:t, description=:d WHERE id=:id');
            $req->execute([
                'id' => $id,
                't'  => $demande->getTitre(),
                'd'  => $demande->getDescription()
            ]);
        } catch (Exception $e) {
            die('Erreur update: ' . $e->getMessage());
        }
    }

    public function getDemande($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM demande WHERE id=:id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die('Erreur getDemande: ' . $e->getMessage());
        }
    }
}
