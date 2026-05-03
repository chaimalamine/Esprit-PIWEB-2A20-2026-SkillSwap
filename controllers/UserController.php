<?php

require_once __DIR__ . '/../models/User.php';

function creerTableNotifications(PDO $conn): void
{
    $conn->exec(
        'CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_code VARCHAR(50) NOT NULL,
            message VARCHAR(255) NOT NULL,
            type VARCHAR(30) NOT NULL DEFAULT "info",
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            reference_type VARCHAR(50) DEFAULT NULL,
            reference_value VARCHAR(100) DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
}

function notificationExiste(PDO $conn, string $userCode, string $referenceType, string $referenceValue): bool
{
    creerTableNotifications($conn);
    $stmt = $conn->prepare(
        'SELECT COUNT(*) FROM notifications WHERE user_code = ? AND reference_type = ? AND reference_value = ?'
    );
    $stmt->execute([$userCode, $referenceType, $referenceValue]);

    return (int) $stmt->fetchColumn() > 0;
}

function ajouterNotification(
    PDO $conn,
    string $userCode,
    string $message,
    string $type = 'info',
    ?string $referenceType = null,
    ?string $referenceValue = null
): void {
    creerTableNotifications($conn);

    if ($referenceType !== null && $referenceValue !== null) {
        if (notificationExiste($conn, $userCode, $referenceType, $referenceValue)) {
            return;
        }
    }

    $stmt = $conn->prepare(
        'INSERT INTO notifications (user_code, message, type, reference_type, reference_value) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userCode, $message, $type, $referenceType, $referenceValue]);
}

function recupererNotificationsUtilisateur(PDO $conn, string $userCode): array
{
    creerTableNotifications($conn);
    $stmt = $conn->prepare(
        'SELECT id, message, type, created_at FROM notifications WHERE user_code = ? AND is_read = 0 ORDER BY created_at DESC, id DESC'
    );
    $stmt->execute([$userCode]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function marquerNotificationsCommeLues(PDO $conn, string $userCode): void
{
    creerTableNotifications($conn);
    $stmt = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_code = ? AND is_read = 0');
    $stmt->execute([$userCode]);
}

function nettoyerTexteSaisie(string $valeur): string
{
    return trim($valeur);
}

function emailExiste(PDO $conn, string $email): bool
{
    $stmt = $conn->prepare('SELECT COUNT(*) FROM g_offre WHERE email = ?');
    $stmt->execute([$email]);

    return (int) $stmt->fetchColumn() > 0;
}

function validerNomOuPrenom(string $valeur): bool
{
    return (bool) preg_match('/^[\p{L}\s\'-]{2,50}$/u', $valeur);
}

function validerMotDePasse(string $motDePasse): bool
{
    if (strlen($motDePasse) < 8) {
        return false;
    }

    $hasLetter = preg_match('/[A-Za-z]/', $motDePasse);
    $hasDigit = preg_match('/\d/', $motDePasse);

    return (bool) ($hasLetter && $hasDigit);
}

function validerInscription(PDO $conn, string $nom, string $prenom, string $email, string $mdp): array
{
    $erreurs = [];

    if ($nom === '' || $prenom === '' || $email === '' || $mdp === '') {
        $erreurs[] = 'Tous les champs sont obligatoires.';
    }

    if ($nom !== '' && !validerNomOuPrenom($nom)) {
        $erreurs[] = 'Le nom doit contenir uniquement des lettres et au moins 2 caracteres.';
    }

    if ($prenom !== '' && !validerNomOuPrenom($prenom)) {
        $erreurs[] = 'Le prenom doit contenir uniquement des lettres et au moins 2 caracteres.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L adresse email n est pas valide.';
    }

    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && emailExiste($conn, $email)) {
        $erreurs[] = 'Cette adresse email existe deja.';
    }

    if ($mdp !== '' && !validerMotDePasse($mdp)) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 8 caracteres avec des lettres et des chiffres.';
    }

    return $erreurs;
}

function insererUtilisateur(PDO $conn, User $user): void
{
    $stmt = $conn->prepare(
        "INSERT INTO g_offre
        (nom, prenom, email, mot_de_passe, date_inscription, statut, code_parrain_unique, parraine_par, credits_gratuits, offre_code)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $user->getNom(),
        $user->getPrenom(),
        $user->getEmail(),
        $user->getMotDePasse(),
        $user->getStatut(),
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

function decrementerParticipantsOffre(PDO $conn, string $codeOffre): void
{
    $stmt = $conn->prepare(
        'UPDATE offre
         SET participants = CASE WHEN participants > 0 THEN participants - 1 ELSE 0 END
         WHERE code_offre = ?'
    );
    $stmt->execute([$codeOffre]);
}

function utilisateurParticipeOffre(PDO $conn, string $codeUser, string $codeOffre): bool
{
    $stmt = $conn->prepare(
        'SELECT COUNT(*) FROM g_offre WHERE code_parrain_unique = ? AND offre_code = ?'
    );
    $stmt->execute([$codeUser, $codeOffre]);

    return (int) $stmt->fetchColumn() > 0;
}

function associerUtilisateurAOffre(PDO $conn, string $codeUser, string $codeOffre): void
{
    $stmt = $conn->prepare(
        'UPDATE g_offre SET offre_code = ? WHERE code_parrain_unique = ?'
    );
    $stmt->execute([$codeOffre, $codeUser]);
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
        'SELECT COUNT(*) FROM g_offre
         WHERE parraine_par = ?
         AND offre_code = ?'
    );
    $stmt->execute([$codeUser, $codeOffre]);

    return (int) $stmt->fetchColumn();
}

function recupererUtilisateursEnAttente(PDO $conn): array
{
    $stmt = $conn->query(
        "SELECT id, nom, prenom, email, code_parrain_unique, parraine_par, offre_code, date_inscription
         FROM g_offre
         WHERE statut = 'en_attente'
         ORDER BY date_inscription ASC"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function validerUtilisateurInvite(PDO $conn, int $id): void
{
    $stmt = $conn->prepare("SELECT * FROM g_offre WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || ($user['statut'] ?? '') === 'actif') {
        header('Location: index.php?page=backoffice');
        exit;
    }

    $stmt = $conn->prepare("UPDATE g_offre SET statut = 'actif' WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php?page=backoffice');
    exit;
}

function crediterParrainSiObjectifAtteint(PDO $conn, string $codeUser, string $codeOffre): ?array
{
    $offre = rechercherOffreParCode($conn, $codeOffre);

    if ($offre === null) {
        return null;
    }

    $invitationsRequises = (int) ($offre['invitations_requises'] ?? 0);
    $creditsGagnes = (int) round((float) ($offre['recompense_parrain'] ?? 0));

    if ($invitationsRequises <= 0 || $creditsGagnes <= 0) {
        return null;
    }

    if (aDejaGagneRecompensePourOffre($conn, $codeUser, $codeOffre)) {
        return null;
    }

    $totalInvitations = compterInvitationsParrain($conn, $codeUser, $codeOffre);

    if ($totalInvitations < $invitationsRequises) {
        return null;
    }

    $stmt = $conn->prepare(
        'UPDATE g_offre SET credits_gratuits = credits_gratuits + ? WHERE code_parrain_unique = ?'
    );
    $stmt->execute([$creditsGagnes, $codeUser]);

    $stmt = $conn->prepare(
        'INSERT INTO recompenses_parrainage (user_code, offre_code, credits_gagnes) VALUES (?, ?, ?)'
    );
    $stmt->execute([$codeUser, $codeOffre, $creditsGagnes]);

    return [
        'credits' => $creditsGagnes,
        'offre_code' => $codeOffre,
        'offre_titre' => (string) ($offre['titre'] ?? $codeOffre),
    ];
}

function construireLienPersonnel(string $codeUser, string $codeOffre = ''): string
{
    $url = APP_BASE_URL . '/index.php?page=inscription&ref=' . urlencode($codeUser);

    if ($codeOffre !== '') {
        $url .= '&offer=' . urlencode($codeOffre);
    }

    return $url;
}

function construireLienParrainageComplet(string $codeUser, string $codeOffre): string
{
    return APP_BASE_URL
        . '/index.php?page=inscription&ref='
        . urlencode($codeUser)
        . '&offer='
        . urlencode($codeOffre);
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

    $user['lien_personnel'] = construireLienPersonnel($codeUser, (string) ($user['offre_code'] ?? ''));
    $user['notifications'] = recupererNotificationsUtilisateur($conn, $codeUser);

    return $user;
}

function resoudreContexteParrainage(PDO $conn, string $ref, string $offerParam = ''): array
{
    $offreCode = '';
    $parrainCode = '';

    if ($offerParam !== '') {
        $offre = rechercherOffreParCode($conn, $offerParam);

        if ($offre !== null) {
            $offreCode = $offerParam;
        }
    }

    if ($ref === '') {
        return [$offreCode, $parrainCode];
    }

    $offre = rechercherOffreParCode($conn, $ref);
    if ($offre !== null) {
        return [$ref, ''];
    }

    $parrain = rechercherUtilisateurParCode($conn, $ref);
    if ($parrain !== null) {
        if ($offreCode === '') {
            $offreCode = (string) ($parrain['offre_code'] ?? '');
        }

        return [$offreCode, $ref];
    }

    return [$offreCode, ''];
}

function afficherInscription(PDO $conn): void
{
    $ref = trim($_GET['ref'] ?? '');
    $message = '';
    $success = false;
    $errors = [];
    $old = [
        'nom' => '',
        'prenom' => '',
        'email' => '',
    ];
    $offerParam = trim($_GET['offer'] ?? '');
    [$offreCode, $parrainCode] = resoudreContexteParrainage($conn, $ref, $offerParam);
    $ref = $parrainCode !== '' ? $parrainCode : $offreCode;
    $utilisateurActuel = recupererInfosUtilisateurActuel($conn);
    $notifications = $utilisateurActuel['notifications'] ?? [];

    require __DIR__ . '/../views/frontoffice/inscription.php';
}

function inscrireUtilisateur(PDO $conn): void
{
    $ref = trim($_GET['ref'] ?? ($_POST['parraine_par'] ?? ''));
    $message = '';
    $success = false;
    $errors = [];
    $utilisateurActuel = null;

    $nom = nettoyerTexteSaisie($_POST['nom'] ?? '');
    $prenom = nettoyerTexteSaisie($_POST['prenom'] ?? '');
    $email = nettoyerTexteSaisie($_POST['email'] ?? '');
    $mdp = (string) ($_POST['mot_de_passe'] ?? '');
    $offerParam = trim($_GET['offer'] ?? ($_POST['offer_code'] ?? ''));
    $old = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
    ];
    [$offreCode, $parrain] = resoudreContexteParrainage($conn, trim($_POST['parraine_par'] ?? $ref), $offerParam);
    $ref = $parrain !== '' ? $parrain : $offreCode;
    $errors = validerInscription($conn, $nom, $prenom, $email, $mdp);

    if ($errors !== []) {
        $message = implode(' ', $errors);
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
        $user->setStatut('en_attente');

        insererUtilisateur($conn, $user);

        if ($parrain !== '') {
            ajouterNotification(
                $conn,
                $parrain,
                'Un participant est inscrit via votre code.',
                'info',
                'inscription_filleul',
                $user->getCodeParrainUnique()
            );

            $recompense = crediterParrainSiObjectifAtteint($conn, $parrain, $offreCode);

            if ($recompense !== null) {
                ajouterNotification(
                    $conn,
                    $parrain,
                    'Objectif atteint pour l offre ' . $recompense['offre_titre'] . '.',
                    'success',
                    'objectif_atteint',
                    $parrain . '_' . $recompense['offre_code']
                );
            }
        }

        $success = true;
        $message = 'Inscription en attente d etre approuvee par l admin du site.';
        $utilisateurActuel = null;
        $notifications = [];
        $old = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
        ];
    }

    $notifications = $notifications ?? [];
    require __DIR__ . '/../views/frontoffice/inscription.php';
}

function participerOffreUtilisateurActuel(PDO $conn, string $codeOffre): void
{
    header('Content-Type: application/json; charset=UTF-8');

    $codeUser = trim($_SESSION['current_user_code'] ?? '');
    $offre = rechercherOffreParCode($conn, $codeOffre);

    if ($offre === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Offre introuvable.',
        ]);
        exit;
    }

    if ($codeUser === '') {
        echo json_encode([
            'success' => true,
            'link' => APP_BASE_URL . '/index.php?page=inscription&ref=' . urlencode($codeOffre),
            'message' => 'Inscrivez-vous d abord avec ce lien pour devenir participant a cette offre.',
        ]);
        exit;
    }

    $user = rechercherUtilisateurParCode($conn, $codeUser);

    if ($user === null) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur introuvable.',
        ]);
        exit;
    }

    $ancienneOffreCode = trim((string) ($user['offre_code'] ?? ''));

    if (!utilisateurParticipeOffre($conn, $codeUser, $codeOffre)) {
        if ($ancienneOffreCode !== '' && $ancienneOffreCode !== $codeOffre) {
            decrementerParticipantsOffre($conn, $ancienneOffreCode);
        }

        associerUtilisateurAOffre($conn, $codeUser, $codeOffre);
        incrementerParticipantsOffre($conn, $codeOffre);
    }

    $lien = construireLienParrainageComplet($codeUser, $codeOffre);

    echo json_encode([
        'success' => true,
        'message' => 'Participation enregistree. Vous pouvez maintenant partager votre lien.',
        'link' => $lien,
    ]);
    exit;
}
