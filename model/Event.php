<?php
class Event {
    private $id_evenement;
    private $titre;
    private $description;
    private $date_debut;
    private $date_fin;
    private $lieu;
    private $capacite_max;
    private $places_restantes;
    private $id_organisateur;
    private $statut;

    public function __construct($titre = '', $description = '', $date_debut = '', $date_fin = '', $lieu = '', $capacite_max = 0, $places_restantes = 0, $id_organisateur = 0, $statut = 'Actif') {
        $this->titre = $titre;
        $this->description = $description;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->lieu = $lieu;
        $this->capacite_max = $capacite_max;
        $this->places_restantes = $places_restantes;
        $this->id_organisateur = $id_organisateur;
        $this->statut = $statut;
    }


    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getLieu() { return $this->lieu; }
    public function getCapaciteMax() { return $this->capacite_max; }
    public function getPlacesRestantes() { return $this->places_restantes; }
    public function getIdOrganisateur() { return $this->id_organisateur; }
    public function getStatut() { return $this->statut; }
    public function getIdEvenement() { return $this->id_evenement; }
    
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setCapaciteMax($capacite_max) { $this->capacite_max = $capacite_max; }
    public function setPlacesRestantes($places_restantes) { $this->places_restantes = $places_restantes; }
    public function setStatut($statut) { $this->statut = $statut; }
}
?>