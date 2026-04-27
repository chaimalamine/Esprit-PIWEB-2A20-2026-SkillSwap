<?php
class Ressource {
    private $id_ressource;
    private $nom;
    private $type;
    private $description;
    private $quantite_disponible;
    private $quantite_totale;
    private $etat;
    private $id_proprietaire;
    private $date_achat;
    private $statut;
    private $date_creation;

    public function __construct( 
        $nom = '', 
        $type = '', 
        $description = '', 
        $quantite_disponible = 0,
        $quantite_totale = 0,
        $etat = '',
        $id_proprietaire = 0,
        $date_achat = null,
        $statut = 'Disponible'
    ) {
        $this->nom = $nom;
        $this->type = $type;
        $this->description = $description;
        $this->quantite_disponible = $quantite_disponible;
        $this->quantite_totale = $quantite_totale;
        $this->etat = $etat;
        $this->id_proprietaire = $id_proprietaire;
        $this->date_achat = $date_achat;
        $this->statut = $statut;
    }

  
    public function getIdRessource() { return $this->id_ressource; }
    public function getNom() { return $this->nom; }
    public function getType() { return $this->type; }
    public function getDescription() { return $this->description; }
    public function getQuantiteDisponible() { return $this->quantite_disponible; }
    public function getQuantiteTotale() { return $this->quantite_totale; }
    public function getEtat() { return $this->etat; }
    public function getIdProprietaire() { return $this->id_proprietaire; }
    public function getDateAchat() { return $this->date_achat; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->date_creation; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setType($type) { $this->type = $type; }
    public function setDescription($description) { $this->description = $description; }
    public function setQuantiteDisponible($qte) { $this->quantite_disponible = $qte; }
    public function setQuantiteTotale($qte) { $this->quantite_totale = $qte; }
    public function setEtat($etat) { $this->etat = $etat; }
    public function setIdProprietaire($id) { $this->id_proprietaire = $id; }
    public function setDateAchat($date) { $this->date_achat = $date; }
    public function setStatut($statut) { $this->statut = $statut; }
}
?>