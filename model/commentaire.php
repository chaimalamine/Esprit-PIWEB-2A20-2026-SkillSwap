<?php
class commentaire {
    private int $idcom;
    private string $contenu;
    private string $datecom;
    private int $idpost;

    public function __construct(string $contenu, string $datecom, int $idpost) {
        $this->contenu = $contenu;
        $this->datecom = $datecom;
        $this->idpost = $idpost;
    }

    // Getters
    public function getIdcom(): int { return $this->idcom; }
    public function getContenu(): string { return $this->contenu; }
    public function getDatecom(): string { return $this->datecom; }
    public function getIdpost(): int { return $this->idpost; }

    // Setters
    public function setIdcom(int $idcom): void { $this->idcom = $idcom; }
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }
    public function setDatecom(string $datecom): void { $this->datecom = $datecom; }
    public function setIdpost(int $idpost): void { $this->idpost = $idpost; }
}
?>