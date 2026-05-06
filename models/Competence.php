<?php
class Competence {
    private $id_competence;
    private $nom_competence;
    private $niveau;
    private $categorie;
    private $heures_echangees;
    private $date_ajout;
    private $id_utilisateur;

    // Constructeur
    public function __construct($nom_competence = "", $niveau = "", $categorie = "", $heures_echangees = 0, $id_utilisateur = 0) {
        $this->nom_competence = $nom_competence;
        $this->niveau = $niveau;
        $this->categorie = $categorie;
        $this->heures_echangees = $heures_echangees;
        $this->id_utilisateur = $id_utilisateur;
    }

    // Getters
    public function getId_competence() {
        return $this->id_competence;
    }

    public function getNom_competence() {
        return $this->nom_competence;
    }

    public function getNiveau() {
        return $this->niveau;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getHeures_echangees() {
        return $this->heures_echangees;
    }

    public function getDate_ajout() {
        return $this->date_ajout;
    }

    public function getId_utilisateur() {
        return $this->id_utilisateur;
    }

    // Setters
    public function setId_competence($id_competence) {
        $this->id_competence = $id_competence;
    }

    public function setNom_competence($nom_competence) {
        $this->nom_competence = $nom_competence;
    }

    public function setNiveau($niveau) {
        $this->niveau = $niveau;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setHeures_echangees($heures_echangees) {
        $this->heures_echangees = $heures_echangees;
    }

    public function setDate_ajout($date_ajout) {
        $this->date_ajout = $date_ajout;
    }

    public function setId_utilisateur($id_utilisateur) {
        $this->id_utilisateur = $id_utilisateur;
    }
}
?>