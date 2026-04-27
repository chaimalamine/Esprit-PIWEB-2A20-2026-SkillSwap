<?php

class User
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private string $dateInscription;
    private string $statut;
    private string $codeParrainUnique;
    private string $parrainePar;
    private int $creditsGratuits;
    private string $offreCode;

    public function __construct(
        int $id = 0,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        string $motDePasse = '',
        string $dateInscription = '',
        string $statut = '',
        string $codeParrainUnique = '',
        string $parrainePar = '',
        int $creditsGratuits = 0,
        string $offreCode = ''
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->dateInscription = $dateInscription;
        $this->statut = $statut;
        $this->codeParrainUnique = $codeParrainUnique;
        $this->parrainePar = $parrainePar;
        $this->creditsGratuits = $creditsGratuits;
        $this->offreCode = $offreCode;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }

    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    public function setDateInscription(string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function getCodeParrainUnique(): string
    {
        return $this->codeParrainUnique;
    }

    public function setCodeParrainUnique(string $codeParrainUnique): void
    {
        $this->codeParrainUnique = $codeParrainUnique;
    }

    public function getParrainePar(): string
    {
        return $this->parrainePar;
    }

    public function setParrainePar(string $parrainePar): void
    {
        $this->parrainePar = $parrainePar;
    }

    public function getCreditsGratuits(): int
    {
        return $this->creditsGratuits;
    }

    public function setCreditsGratuits(int $creditsGratuits): void
    {
        $this->creditsGratuits = $creditsGratuits;
    }

    public function getOffreCode(): string
    {
        return $this->offreCode;
    }

    public function setOffreCode(string $offreCode): void
    {
        $this->offreCode = $offreCode;
    }
}
