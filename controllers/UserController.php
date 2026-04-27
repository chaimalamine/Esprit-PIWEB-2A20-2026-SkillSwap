<?php

require_once __DIR__ . '/../models/User.php';

function insererUtilisateur(PDO $conn, User $user): void
{
    $stmt = $conn->prepare(
        "INSERT INTO g_offre
        (nom, prenom, email, mot_de_passe, date_inscription, statut, code_parrain_unique, parraine_par, credits_gratuits, offre_code)
        VALUES (?, ?, ?, ?, NOW(), 'actif', ?, ?, ?, ?)"
    );

    $stmt->execute([
        $user->getNom(),
        $user->getPrenom(),
        $user->getEmail(),
        $user->getMotDePasse(),
        $user->getCodeParrainUnique(),
        $user->getParrainePar(),
        $user->getCreditsGratuits(),
        $user->getOffreCode(),
    ]);
}

function ajouterBonusParrain(PDO $conn, string $codeParrain): void
{
    $stmt = $conn->prepare(
        'UPDATE g_offre
         SET score_reputation = score_reputation + 10,
             nombre_avis_recus = nombre_avis_recus + 1
         WHERE code_parrain_unique = ?'
    );
    $stmt->execute([$codeParrain]);
}

function rechercherOffreParCode(PDO $conn, string $codeOffre): ?array
{
    $stmt = $conn->prepare('SELECT * FROM offre WHERE code_offre = ? LIMIT 1');
    $stmt->execute([$codeOffre]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}

function rechercherUtilisateurParCode(PDO $conn, string $codeUser): ?array
{
    $stmt = $conn->prepare('SELECT * FROM g_offre WHERE code_parrain_unique = ? LIMIT 1');
    $stmt->execute([$codeUser]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}

function incrementerParticipantsOffre(PDO $conn, string $codeOffre): void
{
    $stmt = $conn->prepare(
        'UPDATE offre
         SET participants = participants + 1
         WHERE code_offre = ?'
    );
    $stmt->execute([$codeOffre]);
}

function recupererCreditsUtilisateur(PDO $conn, string $codeUser): int
{
    $stmt = $conn->prepare('SELECT credits_gratuits FROM g_offre WHERE code_parrain_unique = ? LIMIT 1');
    $stmt->execute([$codeUser]);
    $credits = $stmt->fetchColumn();

    return $credits !== false ? (int) $credits : 0;
}

function aDejaGagneRecompensePourOffre(PDO $conn, string $codeUser, string $codeOffre): bool
{
    $stmt = $conn->prepare(
        'SELECT COUNT(*) FROM recompenses_parrainage WHERE user_code = ? AND offre_code = ?'
    );
    $stmt->execute([$codeUser, $codeOffre]);

    return (int) $stmt->fetchColumn() > 0;
}

function compterInvitationsParrain(PDO $conn, string $codeUser, string $codeOffre): int
{
    $stmt = $conn->prepare(
        'SELECT COUNT(*) FROM g_offre WHERE parraine_par = ? AND offre_code = ?'
    );
    $stmt->execute([$codeUser, $codeOffre]);

    return (int) $stmt->fetchColumn();
}

function crediterParrainSiObjectifAtteint(PDO $conn, string $codeUser, string $codeOffre): void
{
    $offre = rechercherOffreParCode($conn, $codeOffre);

    if ($offre === null) {
        return;
    }

    $invitationsRequises = (int) ($offre['invitations_requises'] ?? 0);
    $creditsGagnes = (int) round((float) ($offre['recompense_parrain'] ?? 0));

    if ($invitationsRequises <= 0 || $creditsGagnes <= 0) {
        return;
    }

    if (aDejaGagneRecompensePourOffre($conn, $codeUser, $codeOffre)) {
        return;
    }

    $totalInvitations = compterInvitationsParrain($conn, $codeUser, $codeOffre);

    if ($totalInvitations < $invitationsRequises) {
        return;
    }

    $stmt = $conn->prepare(
        'UPDATE g_offre SET credits_gratuits = credits_gratuits + ? WHERE code_parrain_unique = ?'
    );
    $stmt->execute([$creditsGagnes, $codeUser]);

    $stmt = $conn->prepare(
        'INSERT INTO recompenses_parrainage (user_code, offre_code, credits_gagnes) VALUES (?, ?, ?)'
    );
    $stmt->execute([$codeUser, $codeOffre, $creditsGagnes]);
}

function construireLienPersonnel(string $codeUser): string
{
    return 'http://localhost/parrainage-project/index.php?page=inscription&ref=' . urlencode($codeUser);
}

function recupererInfosUtilisateurActuel(PDO $conn): ?array
{
    $codeUser = trim($_SESSION['current_user_code'] ?? '');

    if ($codeUser === '') {
        return null;
    }

    $user = rechercherUtilisateurParCode($conn, $codeUser);

    if ($user === null) {
        return null;
    }

    $user['lien_personnel'] = construireLienPersonnel($codeUser);

    return $user;
}

function resoudreContexteParrainage(PDO $conn, string $ref): array
{
    $offreCode = '';
    $parrainCode = '';

    if ($ref === '') {
        return [$offreCode, $parrainCode];
    }

    $offre = rechercherOffreParCode($conn, $ref);
    if ($offre !== null) {
        return [$ref, ''];
    }

    $parrain = rechercherUtilisateurParCode($conn, $ref);
    if ($parrain !== null) {
        return [(string) ($parrain['offre_code'] ?? ''), $ref];
    }

    return ['', ''];
}

function afficherInscription(PDO $conn): void
{
    $ref = trim($_GET['ref'] ?? '');
    $message = '';
    $success = false;
    [$offreCode, $parrainCode] = resoudreContexteParrainage($conn, $ref);
    $ref = $parrainCode !== '' ? $parrainCode : $offreCode;
    $utilisateurActuel = recupererInfosUtilisateurActuel($conn);

    require __DIR__ . '/../views/frontoffice/inscription.php';
}

function inscrireUtilisateur(PDO $conn): void
{
    $ref = trim($_GET['ref'] ?? ($_POST['parraine_par'] ?? ''));
    $message = '';
    $success = false;
    $utilisateurActuel = null;

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = trim($_POST['mot_de_passe'] ?? '');
    [$offreCode, $parrain] = resoudreContexteParrainage($conn, trim($_POST['parraine_par'] ?? $ref));
    $ref = $parrain !== '' ? $parrain : $offreCode;

    if ($nom === '' || $prenom === '' || $email === '' || $mdp === '') {
        $message = 'Tous les champs sont obligatoires';
    } else {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setMotDePasse($mdp);
        $user->setCodeParrainUnique(strtoupper(substr(md5(uniqid('', true)), 0, 6)));
        $user->setParrainePar($parrain);
        $user->setCreditsGratuits(0);
        $user->setOffreCode($offreCode);

        insererUtilisateur($conn, $user);
        $_SESSION['current_user_code'] = $user->getCodeParrainUnique();

        if ($offreCode !== '') {
            incrementerParticipantsOffre($conn, $offreCode);
        }

        if ($parrain !== '') {
            ajouterBonusParrain($conn, $parrain);
            crediterParrainSiObjectifAtteint($conn, $parrain, $offreCode);
        }

        $success = true;
        $message = 'Inscription en attente de validation admin';
        $utilisateurActuel = recupererInfosUtilisateurActuel($conn);
    }

    require __DIR__ . '/../views/frontoffice/inscription.php';
}
