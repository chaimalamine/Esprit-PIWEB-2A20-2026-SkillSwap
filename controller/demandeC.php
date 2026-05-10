<?php

include_once __DIR__ . '/../config.php';

class DemandeC {

    public function listeDemandes() {
        $db = config::getConnexion();
        try {
            return $db->query('SELECT * FROM demande ORDER BY id DESC');
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function listeDemandesApprouvees() {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('SELECT * FROM demande WHERE statut = :st ORDER BY id DESC');
            $req->execute(['st' => 'approuve']);
            return $req;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function addDemande($demande) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare(
                'INSERT INTO demande (titre, description, competence_souhaitee, urgence, date_creation, statut)
                 VALUES (:t, :d, :cs, :u, :dc, :st)'
            );
            $req->execute([
                't'  => $demande->getTitre(),
                'd'  => $demande->getDescription(),
                'cs' => $demande->getCompetenceSouhaitee(),
                'u'  => $demande->getUrgence(),
                'dc' => $demande->getDateCreation(),
                'st' => $demande->getStatut(),
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
            $req = $db->prepare(
                'UPDATE demande SET titre=:t, description=:d, competence_souhaitee=:cs,
                 urgence=:u, statut=:st WHERE id=:id'
            );
            $req->execute([
                'id' => $id,
                't'  => $demande->getTitre(),
                'd'  => $demande->getDescription(),
                'cs' => $demande->getCompetenceSouhaitee(),
                'u'  => $demande->getUrgence(),
                'st' => $demande->getStatut(),
            ]);
        } catch (Exception $e) {
            die('Erreur update: ' . $e->getMessage());
        }
    }

    public function updateStatutDemande($id, $statut) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare('UPDATE demande SET statut=:st WHERE id=:id');
            $req->execute(['st' => $statut, 'id' => $id]);
        } catch (Exception $e) {
            die('Erreur statut: ' . $e->getMessage());
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

    public function countDemandes() {
        $db = config::getConnexion();
        return (int) $db->query('SELECT COUNT(*) FROM demande')->fetchColumn();
    }

    public function countByStatut($statut) {
        $db  = config::getConnexion();
        $req = $db->prepare('SELECT COUNT(*) FROM demande WHERE statut=:st');
        $req->execute(['st' => $statut]);
        return (int) $req->fetchColumn();
    }
}
