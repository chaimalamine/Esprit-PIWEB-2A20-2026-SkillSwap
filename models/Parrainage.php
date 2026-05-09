<?php
/**
 * Modèle Parrainage - SkillSwap
 * Gère le système de parrainage entre utilisateurs
 */
class Parrainage {
    private $id_parrainage;
    private $id_parrain;       // L'utilisateur qui parraine
    private $id_filleul;       // L'utilisateur parrainé (NULL si invitation en attente)
    private $email_invite;     // Email de la personne invitée
    private $code_parrainage;  // Code unique de parrainage
    private $statut;           // 'en_attente', 'accepte', 'expire'
    private $date_invitation;
    private $date_acceptation;
    private $credits_parrain;  // Crédits gagnés par le parrain
    private $credits_filleul;  // Crédits gagnés par le filleul

    public function __construct(
        $id_parrain = 0,
        $email_invite = '',
        $code_parrainage = '',
        $statut = 'en_attente',
        $credits_parrain = 10,
        $credits_filleul = 5
    ) {
        $this->id_parrain = $id_parrain;
        $this->email_invite = $email_invite;
        $this->code_parrainage = $code_parrainage;
        $this->statut = $statut;
        $this->credits_parrain = $credits_parrain;
        $this->credits_filleul = $credits_filleul;
    }

    // Getters
    public function getId_parrainage() { return $this->id_parrainage; }
    public function getId_parrain() { return $this->id_parrain; }
    public function getId_filleul() { return $this->id_filleul; }
    public function getEmail_invite() { return $this->email_invite; }
    public function getCode_parrainage() { return $this->code_parrainage; }
    public function getStatut() { return $this->statut; }
    public function getDate_invitation() { return $this->date_invitation; }
    public function getDate_acceptation() { return $this->date_acceptation; }
    public function getCredits_parrain() { return $this->credits_parrain; }
    public function getCredits_filleul() { return $this->credits_filleul; }

    // Setters
    public function setId_parrainage($id) { $this->id_parrainage = $id; }
    public function setId_parrain($id) { $this->id_parrain = $id; }
    public function setId_filleul($id) { $this->id_filleul = $id; }
    public function setEmail_invite($email) { $this->email_invite = $email; }
    public function setCode_parrainage($code) { $this->code_parrainage = $code; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDate_invitation($date) { $this->date_invitation = $date; }
    public function setDate_acceptation($date) { $this->date_acceptation = $date; }
    public function setCredits_parrain($credits) { $this->credits_parrain = $credits; }
    public function setCredits_filleul($credits) { $this->credits_filleul = $credits; }
}
?>
