<?php
class post {
    private int $idpost;
    private string $titre;
    private string $contenu;
    private string $datepost;
    private int $idgroup;

    public function __construct(string $titre, string $contenu, string $datepost, int $idgroup) {
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->datepost = $datepost;
        $this->idgroup = $idgroup;
    }

    // Getters
    public function getIdpost(): int {
        return $this->idpost;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getDatepost(): string {
        return $this->datepost;
    }

    public function getIdgroup(): int {
        return $this->idgroup;
    }

    // Setters
    public function setIdpost(int $idpost): void {
        $this->idpost = $idpost;
    }

    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    public function setContenu(string $contenu): void {
        $this->contenu = $contenu;
    }

    public function setDatepost(string $datepost): void {
        $this->datepost = $datepost;
    }

    public function setIdgroup(int $idgroup): void {
        $this->idgroup = $idgroup;
    }
}
?>