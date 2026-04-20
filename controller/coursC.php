<?php

include_once __DIR__ . '/../config.php';

class CoursC {

    public function listeCours() {
        $db = config::getConnexion();
        try {
            return $db->query('SELECT * FROM cours ORDER BY id DESC');
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeCoursApprouves() {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM cours WHERE statut = :st ORDER BY id DESC');
            $req->execute(['st' => 'approuve']);
            return $req;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addCours($cours) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                'INSERT INTO cours (titre, description, categorie, niveau, date_creation, statut)
                 VALUES (:t, :d, :cat, :niv, :dc, :st)'
            );
            $req->execute([
                't'   => $cours->getTitre(),
                'd'   => $cours->getDescription(),
                'cat' => $cours->getCategorie(),
                'niv' => $cours->getNiveau(),
                'dc'  => $cours->getDateCreation(),
                'st'  => $cours->getStatut(),
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
            $req = $db->prepare(
                'UPDATE cours SET titre=:t, description=:d, categorie=:cat,
                 niveau=:niv, statut=:st WHERE id=:id'
            );
            $req->execute([
                'id'  => $id,
                't'   => $cours->getTitre(),
                'd'   => $cours->getDescription(),
                'cat' => $cours->getCategorie(),
                'niv' => $cours->getNiveau(),
                'st'  => $cours->getStatut(),
            ]);
        } catch (Exception $e) {
            die('Erreur update: ' . $e->getMessage());
        }
    }

    public function updateStatutCours($id, $statut) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE cours SET statut=:st WHERE id=:id');
            $req->execute(['st' => $statut, 'id' => $id]);
        } catch (Exception $e) {
            die('Erreur statut: ' . $e->getMessage());
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

    public function countCours() {
        $db = config::getConnexion();
        return (int) $db->query('SELECT COUNT(*) FROM cours')->fetchColumn();
    }

    public function countByStatut($statut) {
        $db  = config::getConnexion();
        $req = $db->prepare('SELECT COUNT(*) FROM cours WHERE statut=:st');
        $req->execute(['st' => $statut]);
        return (int) $req->fetchColumn();
    }
}
