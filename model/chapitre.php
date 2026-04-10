<?php

class Chapitre {
    private int $id;
    private int $cours_id;
    private string $titre, $contenu;
    private ?string $fichier_pdf;

    public function __construct(int $cours_id, string $titre, string $contenu, ?string $fichier_pdf = null) {
        $this->cours_id = $cours_id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->fichier_pdf = $fichier_pdf;
    }

    public function getCours_id() { return $this->cours_id; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getFichierPdf() { return $this->fichier_pdf; }
    public function setTitre(string $titre) { $this->titre = $titre; }
    public function setContenu(string $contenu) { $this->contenu = $contenu; }
}
