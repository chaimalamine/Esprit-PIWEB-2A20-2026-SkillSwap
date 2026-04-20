<?php

class Cours {
    private int    $id;
    private string $titre;
    private string $description;
    private string $categorie;
    private string $niveau;
    private string $date_creation;
    private string $statut;

    public function __construct(
        string $titre,
        string $description,
        string $categorie     = '',
        string $niveau        = 'debutant',
        string $date_creation = '',
        string $statut        = 'en_attente'
    ) {
        $this->titre         = $titre;
        $this->description   = $description;
        $this->categorie     = $categorie;
        $this->niveau        = $niveau;
        $this->date_creation = $date_creation ?: date('Y-m-d H:i:s');
        $this->statut        = $statut;
    }

    public function getTitre()        { return $this->titre; }
    public function getDescription()  { return $this->description; }
    public function getCategorie()    { return $this->categorie; }
    public function getNiveau()       { return $this->niveau; }
    public function getDateCreation() { return $this->date_creation; }
    public function getStatut()       { return $this->statut; }

    public function setTitre(string $t)       { $this->titre       = $t; }
    public function setDescription(string $d) { $this->description = $d; }
    public function setCategorie(string $c)   { $this->categorie   = $c; }
    public function setNiveau(string $n)      { $this->niveau      = $n; }
    public function setStatut(string $s)      { $this->statut      = $s; }
}
