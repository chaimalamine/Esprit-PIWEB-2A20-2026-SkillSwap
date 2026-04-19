<?php
class groupe {
    private int $idgroup;
    private string $nom;
    private string $description;
    private string $datecreation;

    public function __construct(string $nom, string $description, string $datecreation) {
        $this->nom = $nom;
        $this->description = $description;
        $this->datecreation = $datecreation;
    }

    public function getIdgroup(): int {
        return $this->idgroup;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDatecreation(): string {
        return $this->datecreation;
    }

    public function setIdgroup(int $idgroup): void {
        $this->idgroup = $idgroup;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setDatecreation(string $datecreation): void {
        $this->datecreation = $datecreation;
    }
}
?>