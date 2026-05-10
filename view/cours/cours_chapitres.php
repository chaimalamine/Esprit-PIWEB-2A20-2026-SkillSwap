<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../controller/chapitreC.php';
include __DIR__ . '/../../model/chapitre.php';

$base     = '/SkillSwap';
$cc       = new CoursC();
$chC      = new ChapitreC();
$cours_id = (int)($_GET['cours_id'] ?? 0);

if (!$cours_id) { header('Location: '.$base.'/view/cours/ajout_cours.php'); exit; }
$cours = $cc->getCours($cours_id);
if (!$cours) { die('Cours introuvable'); }

// ── Ajout d'un chapitre via POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_chapitre') {
    $fichier_pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = time() . '_' . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
        $fichier_pdf = $filename;
    }
    $ch = new Chapitre($cours_id, $_POST['titre'], $_POST['contenu'], $fichier_pdf);
    $chC->addChapitre($ch);
    header('Location: '.$base.'/view/cours/cours_chapitres.php?cours_id='.$cours_id.'&ok=1');
    exit;
}

// ── Suppression d'un chapitre ──
if (isset($_GET['del_chapitre'])) {
    $chC->deleteChapitre((int)$_GET['del_chapitre']);
    header('Location: '.$base.'/view/cours/cours_chapitres.php?cours_id='.$cours_id);
    exit;
}

$chapitres = $chC->listeChapitres($cours_id)->fetchAll();
$ajoutOk   = isset($_GET['ok']);

include __DIR__ . '/../layout_shared.php';
?>

<!-- Stepper -->
<div style="display:flex;align-items:center;gap:0;margin-bottom:32px;background:white;border-radius:14px;padding:20px 28px;border:1px solid #f0eaff;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <!-- Étape 1 — terminée -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#7b2ff7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 1</div>
            <div style="font-size:13px;font-weight:600;color:#7b2ff7;">Informations du cours</div>
        </div>
    </div>
    <div style="flex:1;height:2px;background:linear-gradient(90deg,#7b2ff7,#e9d5ff);margin:0 16px;"></div>
    <!-- Étape 2 — en cours -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7b2ff7,#a855f7);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 0 4px #ede9ff;">
            <span style="color:white;font-size:13px;font-weight:700;">2</span>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 2</div>
            <div style="font-size:13px;font-weight:600;color:#1a0533;">Chapitres</div>
        </div>
    </div>
    <div style="flex:1;height:2px;background:#f0eaff;margin:0 16px;"></div>
    <!-- Étape 3 -->
    <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#f5f3ff;border:2px solid #e9d5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="color:#c4b5fd;font-size:13px;font-weight:700;">3</span>
        </div>
        <div>
            <div style="font-size:12px;color:#9ca3af;">Étape 3</div>
            <div style="font-size:13px;font-weight:500;color:#9ca3af;">Soumettre</div>
        </div>
    </div>
</div>

<!-- Résumé cours -->
<div style="background:linear-gradient(135deg,#7b2ff7,#a855f7);border-radius:16px;padding:24px 28px;margin-bottom:28px;color:white;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
    <div>
        <div style="font-size:12px;opacity:0.75;margin-bottom:4px;text-transform:uppercase;letter-spacing:1px;">Cours créé</div>
        <h2 style="font-size:20px;font-weight:700;margin-bottom:4px;"><?= htmlspecialchars($cours['titre']) ?></h2>
        <div style="font-size:13px;opacity:0.85;display:flex;gap:12px;flex-wrap:wrap;">
            <?php if (!empty($cours['categorie'])): ?><span><?= htmlspecialchars($cours['categorie']) ?></span><?php endif; ?>
            <span><?= ['debutant'=>'Débutant','intermediaire'=>'Intermédiaire','avance'=>'Avancé'][$cours['niveau'] ?? 'debutant'] ?></span>
            <span><?= count($chapitres) ?> chapitre<?= count($chapitres)>1?'s':'' ?></span>
        </div>
    </div>
    <div style="background:rgba(255,255,255,0.15);border-radius:10px;padding:8px 16px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.25);">
        En attente de validation
    </div>
</div>

<?php if ($ajoutOk): ?>
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:13px 18px;margin-bottom:20px;color:#15803d;font-size:14px;font-weight:500;">
    Chapitre ajouté avec succès. Vous pouvez en ajouter d'autres ou soumettre votre cours.
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start;">

    <!-- Colonne gauche : liste chapitres + form ajout -->
    <div>
        <!-- Chapitres existants -->
        <div style="margin-bottom:20px;">
            <h3 style="color:#1a0533;font-size:16px;font-weight:700;margin-bottom:14px;">
                Chapitres ajoutés
                <span style="background:#f5f3ff;color:#7b2ff7;font-size:12px;padding:2px 10px;border-radius:20px;margin-left:8px;font-weight:600;"><?= count($chapitres) ?></span>
            </h3>

            <?php if (empty($chapitres)): ?>
            <div style="background:#fdfcff;border:1px dashed #e9d5ff;border-radius:14px;padding:32px;text-align:center;color:#9ca3af;">
                <div style="font-size:32px;margin-bottom:8px;">📄</div>
                <p style="font-size:14px;">Aucun chapitre encore — ajoutez-en un ci-dessous.</p>
            </div>
            <?php else: ?>
            <?php foreach ($chapitres as $i => $ch): ?>
            <div style="background:white;border:1px solid #f0eaff;border-radius:14px;padding:18px 20px;margin-bottom:10px;display:flex;align-items:flex-start;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#7b2ff7,#a855f7);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;"><?= $i+1 ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:600;color:#1a0533;margin-bottom:3px;"><?= htmlspecialchars($ch['titre']) ?></div>
                    <div style="font-size:13px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($ch['contenu']) ?></div>
                    <?php if (!empty($ch['fichier_pdf'])): ?>
                    <a href="/SkillSwap/uploads/<?= htmlspecialchars($ch['fichier_pdf']) ?>" target="_blank" style="font-size:12px;color:#7b2ff7;text-decoration:none;margin-top:4px;display:inline-block;">PDF joint</a>
                    <?php endif; ?>
                </div>
                <a href="?cours_id=<?= $cours_id ?>&del_chapitre=<?= $ch['id'] ?>" onclick="return confirm('Supprimer ce chapitre ?')"
                   style="color:#dc2626;text-decoration:none;font-size:12px;font-weight:500;flex-shrink:0;padding:4px 10px;border:1px solid #fecaca;border-radius:6px;transition:all 0.15s;"
                   onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                    Supprimer
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Formulaire ajout chapitre -->
        <div style="background:white;border:1px solid #f0eaff;border-radius:16px;padding:28px 26px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <h3 style="color:#1a0533;font-size:15px;font-weight:700;margin-bottom:20px;">Ajouter un chapitre</h3>
            <form id="chapitreForm" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_chapitre">
                <input type="hidden" name="cours_id" value="<?= $cours_id ?>">

                <div class="form-group">
                    <label for="titre">Titre du chapitre <span class="req">*</span></label>
                    <input type="text" id="titre" name="titre" placeholder="Ex : Introduction aux variables">
                    <span id="titreMsg" class="field-msg"></span>
                </div>

                <div class="form-group">
                    <label for="contenu">Contenu <span class="req">*</span></label>
                    <textarea id="contenu" name="contenu" placeholder="Décrivez le contenu de ce chapitre..."></textarea>
                    <span id="contenuMsg" class="field-msg"></span>
                </div>

                <div class="form-group">
                    <label for="pdf">Fichier PDF <span style="color:#9ca3af;font-weight:400;">(optionnel)</span></label>
                    <input type="file" id="pdf" name="pdf" accept=".pdf" style="padding:9px 14px;background:#fdfcff;color:#555;">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-purple">+ Ajouter ce chapitre</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Colonne droite : soumettre -->
    <div style="position:sticky;top:80px;">
        <div style="background:white;border:1px solid #f0eaff;border-radius:16px;padding:28px 24px;box-shadow:0 1px 4px rgba(0,0,0,0.04);margin-bottom:16px;">
            <h3 style="color:#1a0533;font-size:15px;font-weight:700;margin-bottom:6px;">Prêt à soumettre ?</h3>
            <p style="color:#9ca3af;font-size:13px;line-height:1.55;margin-bottom:20px;">
                Votre cours compte <strong style="color:#7b2ff7;"><?= count($chapitres) ?> chapitre<?= count($chapitres)>1?'s':'' ?></strong>.
                Une fois soumis, l'administrateur le validera avant publication.
            </p>
            <?php if (count($chapitres) > 0): ?>
            <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn btn-purple" style="width:100%;justify-content:center;margin-bottom:10px;">
                Soumettre pour validation
            </a>
            <?php else: ?>
            <button class="btn btn-purple" style="width:100%;justify-content:center;opacity:0.5;cursor:not-allowed;" disabled>
                Soumettre pour validation
            </button>
            <p style="font-size:12px;color:#f97316;margin-top:8px;text-align:center;">Ajoutez au moins 1 chapitre</p>
            <?php endif; ?>
            <a href="<?= $base ?>/view/cours/liste_cours.php" style="display:block;text-align:center;font-size:13px;color:#9ca3af;text-decoration:none;margin-top:8px;">
                Sauvegarder et finir plus tard
            </a>
        </div>

        <div style="background:#fdfcff;border:1px solid #f0eaff;border-radius:14px;padding:20px 20px;font-size:13px;color:#6b7280;line-height:1.6;">
            <strong style="color:#1a0533;display:block;margin-bottom:8px;">Que se passe-t-il ensuite ?</strong>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="width:20px;height:20px;background:#f5f3ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#7b2ff7;flex-shrink:0;">1</span>
                    Votre cours passe en revue par l'administrateur
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="width:20px;height:20px;background:#f5f3ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#7b2ff7;flex-shrink:0;">2</span>
                    Si approuvé, il devient visible pour tous
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="width:20px;height:20px;background:#f5f3ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#7b2ff7;flex-shrink:0;">3</span>
                    Vous pouvez modifier le cours à tout moment
                </div>
            </div>
        </div>
    </div>

</div>

</div></body></html>
