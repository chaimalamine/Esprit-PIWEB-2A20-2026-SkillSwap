<?php

class Offre
{
    private int $idOffre;
    private string $titre;
    private string $description;
    private int $participants;
    private string $codeParrainUnique;
    private string $dateDebut;
    private string $dateFin;
    private float $recompenseParrain;
    private int $invitationsRequises;

    public function __construct(
        int $idOffre = 0,
        string $titre = '',
        string $description = '',
        int $participants = 0,
        string $codeParrainUnique = '',
        string $dateDebut = '',
        string $dateFin = '',
        float $recompenseParrain = 0,
        int $invitationsRequises = 0
    ) {
        $this->idOffre = $idOffre;
        $this->titre = $titre;
        $this->description = $description;
        $this->participants = $participants;
        $this->codeParrainUnique = $codeParrainUnique;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->recompenseParrain = $recompenseParrain;
        $this->invitationsRequises = $invitationsRequises;
    }

    public function getIdOffre(): int
    {
        return $this->idOffre;
    }

    public function setIdOffre(int $idOffre): void
    {
        $this->idOffre = $idOffre;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getParticipants(): int
    {
        return $this->participants;
    }

    public function setParticipants(int $participants): void
    {
        $this->participants = $participants;
    }

    public function getCodeParrainUnique(): string
    {
        return $this->codeParrainUnique;
    }

    public function setCodeParrainUnique(string $codeParrainUnique): void
    {
        $this->codeParrainUnique = $codeParrainUnique;
    }

    public function getDateDebut(): string
    {
        return $this->dateDebut;
    }

    public function setDateDebut(string $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): string
    {
        return $this->dateFin;
    }

    public function setDateFin(string $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getRecompenseParrain(): float
    {
        return $this->recompenseParrain;
    }

    public function setRecompenseParrain(float $recompenseParrain): void
    {
        $this->recompenseParrain = $recompenseParrain;
    }

    public function getInvitationsRequises(): int
    {
        return $this->invitationsRequises;
    }

    public function setInvitationsRequises(int $invitationsRequises): void
    {
        $this->invitationsRequises = $invitationsRequises;
    }
}
