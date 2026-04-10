<?php

include_once __DIR__ . '/../config.php';

class ChapitreC {

    public function listeChapitres($cours_id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM chapitre WHERE cours_id=:cid ORDER BY id ASC');
            $req->execute(['cid' => $cours_id]);
            return $req;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addChapitre($chapitre) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('INSERT INTO chapitre VALUES (NULL,:cid,:t,:c,:pdf)');
            $req->execute([
                'cid' => $chapitre->getCours_id(),
                't'   => $chapitre->getTitre(),
                'c'   => $chapitre->getContenu(),
                'pdf' => $chapitre->getFichierPdf()
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteChapitre($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('DELETE FROM chapitre WHERE id=:id');
            $req->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function updateChapitre($chapitre, $id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE chapitre SET titre=:t, contenu=:c, fichier_pdf=:pdf WHERE id=:id');
            $req->execute([
                'id'  => $id,
                't'   => $chapitre->getTitre(),
                'c'   => $chapitre->getContenu(),
                'pdf' => $chapitre->getFichierPdf()
            ]);
        } catch (Exception $e) {
            die('Erreur update: ' . $e->getMessage());
        }
    }

    public function getChapitre($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM chapitre WHERE id=:id');
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) {
            die('Erreur getChapitre: ' . $e->getMessage());
        }
    }
}
