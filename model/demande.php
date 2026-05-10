<?php

class Demande {
    private int    $id;
    private string $titre;
    private string $description;
    private string $competence_souhaitee;
    private string $urgence;
    private string $date_creation;
    private string $statut;

    public function __construct(
        string $titre,
        string $description,
        string $competence_souhaitee = '',
        string $urgence              = 'normale',
        string $date_creation        = '',
        string $statut               = 'en_attente'
    ) {
        $this->titre                = $titre;
        $this->description          = $description;
        $this->competence_souhaitee = $competence_souhaitee;
        $this->urgence              = $urgence;
        $this->date_creation        = $date_creation ?: date('Y-m-d H:i:s');
        $this->statut               = $statut;
    }

    public function getTitre()               { return $this->titre; }
    public function getDescription()         { return $this->description; }
    public function getCompetenceSouhaitee() { return $this->competence_souhaitee; }
    public function getUrgence()             { return $this->urgence; }
    public function getDateCreation()        { return $this->date_creation; }
    public function getStatut()              { return $this->statut; }

    public function setTitre(string $t)               { $this->titre               = $t; }
    public function setDescription(string $d)         { $this->description         = $d; }
    public function setCompetenceSouhaitee(string $c) { $this->competence_souhaitee = $c; }
    public function setUrgence(string $u)             { $this->urgence             = $u; }
    public function setStatut(string $s)              { $this->statut              = $s; }
}
