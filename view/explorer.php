<?php
include __DIR__ . '/../controller/coursC.php';
include __DIR__ . '/../controller/demandeC.php';

$cc     = new CoursC();
$dc     = new DemandeC();
$base   = '/SkillSwap';

// Onglet actif : 'cours' ou 'demandes'
$tab = ($_GET['tab'] ?? 'cours') === 'demandes' ? 'demandes' : 'cours';

$cours    = $cc->listeCoursApprouves()->fetchAll();
$demandes = $dc->listeDemandesApprouvees()->fetchAll();

include __DIR__ . '/layout_shared.php';
?>

<!-- Hero -->
<div style="background:linear-gradient(135deg,#7b2ff7,#a855f7 60%,#c084fc);border-radius:20px;padding:40px 44px;margin-bottom:28px;color:white;">
    <h1 style="font-size:28px;font-weight:800;margin-bottom:8px;">Explorer la communauté</h1>
    <p style="font-size:15px;opacity:0.85;margin-bottom:28px;">Parcourez les cours et les demandes publiés par la communauté SkillSwap.</p>

    <!-- Tabs -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="?tab=cours"
           style="padding:10px 26px;border-radius:22px;font-size:14px;font-weight:600;text-decoration:none;transition:all 0.2s;
                  <?= $tab==='cours' ? 'background:white;color:#7b2ff7;box-shadow:0 2px 8px rgba(0,0,0,0.15);' : 'background:rgba(255,255,255,0.15);color:white;border:1.5px solid rgba(255,255,255,0.35);' ?>">
            Cours
            <span style="margin-left:6px;padding:2px 9px;border-radius:20px;font-size:12px;
                         <?= $tab==='cours' ? 'background:#f5f3ff;color:#7b2ff7;' : 'background:rgba(255,255,255,0.2);color:white;' ?>">
                <?= count($cours) ?>
            </span>
        </a>
        <a href="?tab=demandes"
           style="padding:10px 26px;border-radius:22px;font-size:14px;font-weight:600;text-decoration:none;transition:all 0.2s;
                  <?= $tab==='demandes' ? 'background:white;color:#7b2ff7;box-shadow:0 2px 8px rgba(0,0,0,0.15);' : 'background:rgba(255,255,255,0.15);color:white;border:1.5px solid rgba(255,255,255,0.35);' ?>">
            Demandes
            <span style="margin-left:6px;padding:2px 9px;border-radius:20px;font-size:12px;
                         <?= $tab==='demandes' ? 'background:#f5f3ff;color:#7b2ff7;' : 'background:rgba(255,255,255,0.2);color:white;' ?>">
                <?= count($demandes) ?>
            </span>
        </a>
    </div>
</div>

<!-- Action bar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <p style="color:#9ca3af;font-size:14px;">
        <?php if ($tab === 'cours'): ?>
            <?= count($cours) ?> cours disponible<?= count($cours) > 1 ? 's' : '' ?>
        <?php else: ?>
            <?= count($demandes) ?> demande<?= count($demandes) > 1 ? 's' : '' ?> disponible<?= count($demandes) > 1 ? 's' : '' ?>
        <?php endif; ?>
    </p>
    <?php if ($tab === 'cours'): ?>
        <a href="<?= $base ?>/view/cours/ajout_cours.php" class="btn btn-purple btn-sm">+ Proposer un cours</a>
    <?php else: ?>
        <a href="<?= $base ?>/view/demande/ajout_demande.php" class="btn btn-purple btn-sm">+ Soumettre une demande</a>
    <?php endif; ?>
</div>

<!-- Contenu onglet Cours -->
<?php if ($tab === 'cours'): ?>
    <?php if (empty($cours)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucun cours disponible</h3>
        <p>Soyez le premier à proposer un cours — il sera visible après validation.</p>
    </div>
    <?php else: ?>
    <?php foreach ($cours as $c):
        $niveauLabel = ['debutant'=>'Débutant','intermediaire'=>'Intermédiaire','avance'=>'Avancé'][$c['niveau'] ?? 'debutant'];
    ?>
    <div class="card">
        <h3><?= htmlspecialchars($c['titre']) ?></h3>
        <div class="card-meta">
            <?php if (!empty($c['categorie'])): ?>
            <span class="tag"><?= htmlspecialchars($c['categorie']) ?></span>
            <?php endif; ?>
            <span class="tag"><?= $niveauLabel ?></span>
        </div>
        <p><?= htmlspecialchars($c['description']) ?></p>
        <div class="actions">
            <a href="<?= $base ?>/view/cours/detail_cours.php?id=<?= $c['id'] ?>" class="btn btn-purple btn-sm">Voir le cours</a>
            <a href="<?= $base ?>/view/cours/update_cours.php?id=<?= $c['id'] ?>" class="btn btn-gray btn-sm">Modifier</a>
            <a href="<?= $base ?>/view/cours/delete_cours.php?id=<?= $c['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

<!-- Contenu onglet Demandes -->
<?php else: ?>
    <?php if (empty($demandes)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Aucune demande disponible</h3>
        <p>Soumettez une demande — elle sera visible après validation par l'administrateur.</p>
    </div>
    <?php else: ?>
    <?php foreach ($demandes as $d):
        $urgenceLabel = ['normale'=>'Normale','urgent'=>'Urgent','flexible'=>'Flexible'][$d['urgence'] ?? 'normale'];
        $urgenceStyle = match($d['urgence'] ?? 'normale') {
            'urgent'   => 'background:#fff1f2;color:#e11d48;',
            'flexible' => 'background:#eff6ff;color:#2563eb;',
            default    => 'background:#f5f3ff;color:#7b2ff7;',
        };
    ?>
    <div class="card">
        <h3><?= htmlspecialchars($d['titre']) ?></h3>
        <div class="card-meta">
            <?php if (!empty($d['competence_souhaitee'])): ?>
            <span class="tag"><?= htmlspecialchars($d['competence_souhaitee']) ?></span>
            <?php endif; ?>
            <span class="tag" style="<?= $urgenceStyle ?>"><?= $urgenceLabel ?></span>
        </div>
        <p><?= htmlspecialchars($d['description']) ?></p>
        <div class="actions">
            <a href="<?= $base ?>/view/demande/update_demande.php?id=<?= $d['id'] ?>" class="btn btn-gray btn-sm">Modifier</a>
            <a href="<?= $base ?>/view/demande/delete_demande.php?id=<?= $d['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Supprimer cette demande ?')">Supprimer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

</div></body></html>
