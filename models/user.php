<?php

class User {
    private $id_utilisateur;
    private $nom;
    private $prenom;
    private $email;
    private $mot_de_passe;
    private $competences;
    private $date_inscription;
    private $statut;
    
    private $score_reputation;
    private $nombre_avis_recus;
    private $badge_confiance;
    private $role;





    // Constructeur
   public function __construct($nom="", $prenom="", $email="", $mot_de_passe="") {
    $this->nom = $nom;
    $this->prenom = $prenom;
    $this->email = $email;
    $this->mot_de_passe = $mot_de_passe;
}

    // Getters
    public function getRole() {
    return $this->role;
}
    public function getId_utilisateur() {
        return $this->id_utilisateur;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getMot_de_passe() {
        return $this->mot_de_passe;
    }

    

    public function getCompetences() {
        return $this->competences;
    }

    public function getDate_inscription() {
        return $this->date_inscription;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getScore_reputation() {
        return $this->score_reputation;
    }

    public function getNombre_avis_recus() {
        return $this->nombre_avis_recus;
    }

    public function getBadge_confiance() {
        return $this->badge_confiance;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setMot_de_passe($mot_de_passe) {
        $this->mot_de_passe = $mot_de_passe;
    }
    public function setRole($role) {
        $this->role = $role;
    }

    public function setCompetences($competences) {
        $this->competences = $competences;
    }

    public function setDate_inscription($date_inscription) {
        $this->date_inscription = $date_inscription;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function setScore_reputation($score_reputation) {
        $this->score_reputation = $score_reputation;
    }

    public function setNombre_avis_recus($nombre_avis_recus) {
        $this->nombre_avis_recus = $nombre_avis_recus;
    }

    public function setBadge_confiance($badge_confiance) {
        $this->badge_confiance = $badge_confiance;
    }
}
?>