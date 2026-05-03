<?php

require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../controllers/UserController.php';

function creerObjetOffre(array $data): Offre
{
    return new Offre(
        (int) ($data['id_offre'] ?? 0),
        (string) ($data['titre'] ?? ''),
        (string) ($data['description'] ?? ''),
        (int) ($data['participants'] ?? 0),
        (string) ($data['code_offre'] ?? ''),
        (string) ($data['date_debut'] ?? ''),
        (string) ($data['date_fin'] ?? ''),
        (float) ($data['recompense_parrain'] ?? 0),
        (int) ($data['invitations_requises'] ?? 0),
        (int) ($data['total_inscriptions_liees'] ?? 0),
        (int) ($data['total_parrainages_lies'] ?? 0)
    );
}

function getSelectOffresAvecJointure(): string
{
    return "
        SELECT
            o.*,
            COUNT(g.code_parrain_unique) AS total_inscriptions_liees,
            SUM(CASE WHEN g.parraine_par IS NOT NULL AND g.parraine_par <> '' THEN 1 ELSE 0 END) AS total_parrainages_lies
        FROM offre o
        LEFT JOIN g_offre g ON o.code_offre = g.offre_code
    ";
}

function synchroniserCodesOffres(PDO $conn): void
{
    $conn->exec("UPDATE offre SET code_offre = CONCAT('OFFRE', id_offre) WHERE code_offre IS NULL OR code_offre = ''");
}

function supprimerOffresExpirees(PDO $conn): void
{
    $stmt = $conn->prepare(
        "DELETE FROM offre
         WHERE date_fin IS NOT NULL
         AND date_fin <> ''
         AND date_fin <> '0000-00-00'
         AND date_fin < ?"
    );
    $stmt->execute([date('Y-m-d')]);
}

function recupererToutesLesOffres(PDO $conn): array
{
    supprimerOffresExpirees($conn);
    synchroniserCodesOffres($conn);
    $stmt = $conn->query(
        getSelectOffresAvecJointure() . '
        GROUP BY
            o.id_offre, o.code_offre, o.titre, o.description, o.recompense_parrain,
            o.recompense_filleul, o.invitations_requises, o.date_debut, o.date_fin,
            o.statut, o.participants
        ORDER BY o.id_offre DESC'
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $offres = [];

    foreach ($rows as $row) {
        $offres[] = creerObjetOffre($row);
    }

    return $offres;
}

function rechercherOffres(PDO $conn, string $motCle): array
{
    supprimerOffresExpirees($conn);
    synchroniserCodesOffres($conn);
    $stmt = $conn->prepare(
        getSelectOffresAvecJointure() . '
        WHERE o.titre LIKE ? OR o.description LIKE ?
        GROUP BY
            o.id_offre, o.code_offre, o.titre, o.description, o.recompense_parrain,
            o.recompense_filleul, o.invitations_requises, o.date_debut, o.date_fin,
            o.statut, o.participants
        ORDER BY o.id_offre DESC'
    );
    $like = '%' . $motCle . '%';
    $stmt->execute([$like, $like]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $offres = [];

    foreach ($rows as $row) {
        $offres[] = creerObjetOffre($row);
    }

    return $offres;
}

function encoderTextePdf(string $texte): string
{
    $texte = str_replace(["\r\n", "\r"], "\n", trim($texte));
    $texte = preg_replace("/[^\P{C}\n\t]/u", '', $texte) ?? '';
    $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $texte);

    if ($converted === false) {
        $converted = $texte;
    }

    return str_replace(
        ['\\', '(', ')'],
        ['\\\\', '\(', '\)'],
        $converted
    );
}

function decouperTextePdf(string $texte, int $longueurMax = 85): array
{
    $texte = trim($texte);

    if ($texte === '') {
        return [''];
    }

    $lignes = preg_split("/\r\n|\r|\n/", $texte) ?: [''];
    $resultat = [];

    foreach ($lignes as $ligne) {
        $ligne = trim($ligne);

        if ($ligne === '') {
            $resultat[] = '';
            continue;
        }

        $morceaux = wordwrap($ligne, $longueurMax, "\n", true);

        foreach (explode("\n", $morceaux) as $morceau) {
            $resultat[] = $morceau;
        }
    }

    return $resultat;
}

function construirePdfSimple(array $pages): string
{
    $objets = [];
    $ajouterObjet = static function (string $contenu) use (&$objets): int {
        $objets[] = $contenu;
        return count($objets);
    };

    $fontObjectId = $ajouterObjet("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>");
    $pageIds = [];

    foreach ($pages as $operations) {
        $stream = implode("\n", $operations) . "\n";
        $streamObjectId = $ajouterObjet(
            "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream"
        );

        $pageIds[] = $ajouterObjet(
            "<< /Type /Page /Parent {{PAGES_ID}} 0 R /MediaBox [0 0 595 842] /Contents "
            . $streamObjectId
            . " 0 R /Resources << /Font << /F1 "
            . $fontObjectId
            . " 0 R >> >> >>"
        );
    }

    $kids = implode(' ', array_map(static fn (int $id): string => $id . ' 0 R', $pageIds));
    $pagesObjectId = $ajouterObjet("<< /Type /Pages /Kids [ $kids ] /Count " . count($pageIds) . " >>");

    foreach ($pageIds as $pageId) {
        $objets[$pageId - 1] = str_replace('{{PAGES_ID}}', (string) $pagesObjectId, $objets[$pageId - 1]);
    }

    $catalogObjectId = $ajouterObjet("<< /Type /Catalog /Pages " . $pagesObjectId . " 0 R >>");

    $pdf = "%PDF-1.4\n";
    $offsets = [0];

    foreach ($objets as $index => $objet) {
        $offsets[] = strlen($pdf);
        $pdf .= ($index + 1) . " 0 obj\n" . $objet . "\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objets) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";

    for ($i = 1; $i <= count($objets); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer\n<< /Size " . (count($objets) + 1) . " /Root " . $catalogObjectId . " 0 R >>\n";
    $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

    return $pdf;
}

function exporterOffresPdf(PDO $conn): void
{
    supprimerOffresExpirees($conn);
    $search = trim($_GET['search'] ?? '');
    $offres = $search !== '' ? rechercherOffres($conn, $search) : recupererToutesLesOffres($conn);

    $pages = [];
    $operations = ['BT', '/F1 18 Tf', '1 0 0 1 50 800 Tm', '(' . encoderTextePdf('Liste des offres de parrainage') . ') Tj'];
    $y = 775;

    if ($search !== '') {
        $operations[] = '/F1 11 Tf';
        $operations[] = '1 0 0 1 50 ' . $y . ' Tm';
        $operations[] = '(' . encoderTextePdf('Recherche : ' . $search) . ') Tj';
        $y -= 24;
    }

    $operations[] = '/F1 11 Tf';
    $operations[] = '1 0 0 1 50 ' . $y . ' Tm';
    $operations[] = '(' . encoderTextePdf('Genere le ' . date('d/m/Y H:i')) . ') Tj';
    $y -= 28;

    if ($offres === []) {
        $operations[] = '1 0 0 1 50 ' . $y . ' Tm';
        $operations[] = '(' . encoderTextePdf('Aucune offre trouvee.') . ') Tj';
    } else {
        foreach ($offres as $index => $offre) {
            $bloc = array_merge(
                ['#' . $offre->getIdOffre() . ' - ' . $offre->getTitre()],
                decouperTextePdf($offre->getDescription(), 88),
                ['Participants : ' . $offre->getParticipants()]
            );

            $hauteurBloc = count($bloc) * 16 + 18;

            if ($y - $hauteurBloc < 60) {
                $operations[] = 'ET';
                $pages[] = $operations;
                $operations = ['BT', '/F1 11 Tf'];
                $y = 800;
            }

            foreach ($bloc as $lineIndex => $ligne) {
                $texte = $lineIndex === 0 ? $ligne : '   ' . $ligne;
                $operations[] = '1 0 0 1 50 ' . $y . ' Tm';
                $operations[] = '(' . encoderTextePdf($texte) . ') Tj';
                $y -= 16;
            }

            if ($index < count($offres) - 1) {
                $y -= 10;
            }
        }
    }

    $operations[] = 'ET';
    $pages[] = $operations;
    $pdf = construirePdfSimple($pages);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="offres-parrainage.pdf"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}

function insererOffre(PDO $conn, Offre $offre): void
{
    $codeOffre = 'OFFRE' . strtoupper(substr(md5(uniqid('', true)), 0, 6));
    $stmt = $conn->prepare(
        'INSERT INTO offre (code_offre, titre, description, recompense_parrain, recompense_filleul, invitations_requises, date_debut, date_fin, statut, participants) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $codeOffre,
        $offre->getTitre(),
        $offre->getDescription(),
        $offre->getRecompenseParrain(),
        0,
        $offre->getInvitationsRequises(),
        $offre->getDateDebut(),
        $offre->getDateFin(),
        $offre->getDateFin() !== '' && $offre->getDateFin() < date('Y-m-d') ? 'inactive' : 'active',
        $offre->getParticipants(),
    ]);
}

function mettreAJourOffre(PDO $conn, Offre $offre): void
{
    $stmt = $conn->prepare(
        'UPDATE offre SET titre = ?, description = ? WHERE id_offre = ?'
    );
    $stmt->execute([
        $offre->getTitre(),
        $offre->getDescription(),
        $offre->getIdOffre(),
    ]);
}

function supprimerOffreBase(PDO $conn, int $idOffre): void
{
    $stmt = $conn->prepare('DELETE FROM offre WHERE id_offre = ?');
    $stmt->execute([$idOffre]);
}

function calculerStatistiquesOffres(array $offres): array
{
    $nombreOffres = count($offres);
    $totalParticipants = 0;
    $maxParticipants = 0;
    $offreTop = null;

    foreach ($offres as $offre) {
        $participants = $offre->getParticipants();
        $totalParticipants += $participants;

        if ($participants >= $maxParticipants) {
            $maxParticipants = $participants;
            $offreTop = $offre;
        }
    }

    return [
        'nombreOffres' => $nombreOffres,
        'totalParticipants' => $totalParticipants,
        'moyenneParticipants' => $nombreOffres > 0 ? round($totalParticipants / $nombreOffres, 1) : 0,
        'maxParticipants' => $maxParticipants,
        'offreTop' => $offreTop,
    ];
}

function calculerProgressionUtilisateurOffres(PDO $conn, array $offres, ?array $utilisateurActuel): array
{
    if ($utilisateurActuel === null) {
        return [];
    }

    $codeUser = trim((string) ($utilisateurActuel['code_parrain_unique'] ?? ''));

    if ($codeUser === '') {
        return [];
    }

    $progressions = [];

    foreach ($offres as $offre) {
        $codeOffre = $offre->getCodeParrainUnique();
        $invitationsRequises = $offre->getInvitationsRequises();
        $invitations = 0;
        $objectifAtteint = false;

        if ($codeOffre !== '') {
            $invitations = compterInvitationsParrain($conn, $codeUser, $codeOffre);
            $objectifAtteint = $invitationsRequises > 0 && $invitations >= $invitationsRequises;
        }

        $progressions[$codeOffre] = [
            'invitations' => $invitations,
            'invitations_requises' => $invitationsRequises,
            'credits_a_gagner' => (int) round($offre->getRecompenseParrain()),
            'statut' => $objectifAtteint ? 'objectif atteint' : 'en cours',
            'objectif_atteint' => $objectifAtteint,
        ];
    }

    return $progressions;
}

function verifierNotificationsOffresExpirees(PDO $conn, ?array $utilisateurActuel): void
{
    if ($utilisateurActuel === null) {
        return;
    }

    $offreCode = trim((string) ($utilisateurActuel['offre_code'] ?? ''));

    if ($offreCode === '') {
        return;
    }

    $offre = rechercherOffreParCode($conn, $offreCode);

    if ($offre === null) {
        return;
    }

    $dateFin = trim((string) ($offre['date_fin'] ?? ''));

    if ($dateFin === '' || $dateFin === '0000-00-00') {
        return;
    }

    if ($dateFin < date('Y-m-d')) {
        ajouterNotification(
            $conn,
            (string) $utilisateurActuel['code_parrain_unique'],
            'Offre expiree : ' . (string) ($offre['titre'] ?? $offreCode) . '.',
            'warning',
            'offre_expiree',
            $offreCode
        );
    }
}

function afficherBackoffice(PDO $conn): void
{
    $search = trim($_GET['search'] ?? '');
    $offres = $search !== '' ? rechercherOffres($conn, $search) : recupererToutesLesOffres($conn);
    $utilisateursEnAttente = recupererUtilisateursEnAttente($conn);

    require __DIR__ . '/../views/backoffice/parrainage.php';
}

function afficherFrontoffice(PDO $conn): void
{
    $offres = recupererToutesLesOffres($conn);
    $statistiques = calculerStatistiquesOffres($offres);
    $flashSuccess = $_SESSION['flash_success'] ?? '';
    unset($_SESSION['flash_success']);
    $utilisateurActuel = function_exists('recupererInfosUtilisateurActuel')
        ? recupererInfosUtilisateurActuel($conn)
        : null;
    verifierNotificationsOffresExpirees($conn, $utilisateurActuel);
    $utilisateurActuel = function_exists('recupererInfosUtilisateurActuel')
        ? recupererInfosUtilisateurActuel($conn)
        : null;
    $notifications = $utilisateurActuel['notifications'] ?? [];
    $progressionsOffres = calculerProgressionUtilisateurOffres($conn, $offres, $utilisateurActuel);

    require __DIR__ . '/../views/frontoffice/gereroffre.php';
}

function ajouterOffre(PDO $conn): void
{
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dateFin = trim($_POST['date_fin'] ?? '');
    $recompense = (float) ($_POST['recompense'] ?? 0);
    $invitationsRequises = (int) ($_POST['invitations_requises'] ?? 0);
    $dateDebut = date('Y-m-d');

    if ($titre !== '' && $description !== '' && $dateFin !== '' && $invitationsRequises > 0) {
        $offre = new Offre();
        $offre->setTitre($titre);
        $offre->setDescription($description);
        $offre->setParticipants(0);
        $offre->setDateDebut($dateDebut);
        $offre->setDateFin($dateFin);
        $offre->setRecompenseParrain($recompense);
        $offre->setInvitationsRequises($invitationsRequises);
        insererOffre($conn, $offre);
    }

    header('Location: index.php?page=backoffice');
    exit;
}

function modifierOffre(PDO $conn): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($id > 0 && $titre !== '' && $description !== '') {
        $offre = new Offre();
        $offre->setIdOffre($id);
        $offre->setTitre($titre);
        $offre->setDescription($description);
        mettreAJourOffre($conn, $offre);
    }

    header('Location: index.php?page=backoffice');
    exit;
}

function supprimerOffre(PDO $conn): void
{
    $id = (int) ($_GET['delete'] ?? 0);

    if ($id > 0) {
        supprimerOffreBase($conn, $id);
    }

    header('Location: index.php?page=backoffice');
    exit;
}
