<?php

class Cours {
    private int $id;
    private string $titre, $description;

    public function __construct(string $titre, string $description) {
        $this->titre = $titre;
        $this->description = $description;
    }

    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function setTitre(string $titre) { $this->titre = $titre; }
    public function setDescription(string $description) { $this->description = $description; }
}
