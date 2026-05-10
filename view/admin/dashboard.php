<?php
include __DIR__ . '/../../controller/coursC.php';
include __DIR__ . '/../../controller/demandeC.php';

$cc = new CoursC();
$dc = new DemandeC();

$totalCours      = $cc->countCours();
$totalDemandes   = $dc->countDemandes();
$approuvesCours  = $cc->countByStatut('approuve');
$enAttenteCours  = $cc->countByStatut('en_attente');
$rejetesCours    = $cc->countByStatut('rejete');
$approuveesDem   = $dc->countByStatut('approuve');
$enAttenteDem    = $dc->countByStatut('en_attente');

include __DIR__ . '/layout_admin.php';
?>

<div style="margin-bottom:28px;">
    <h1 style="color:#1a0533;font-size:24px;font-weight:700;margin-bottom:4px;">Tableau de bord</h1>
    <p style="color:#999;font-size:14px;">Vue d'ensemble de la plateforme SkillSwap</p>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-num"><?= $totalCours ?></div>
        <div class="stat-label">📚 Cours total</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $approuvesCours ?></div>
        <div class="stat-label">✅ Cours publiés</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $enAttenteCours ?></div>
        <div class="stat-label">⏳ Cours en attente</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $totalDemandes ?></div>
        <div class="stat-label">📋 Demandes total</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $approuveesDem ?></div>
        <div class="stat-label">✅ Demandes publiées</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $enAttenteDem ?></div>
        <div class="stat-label">⏳ Demandes en attente</div>
    </div>
</div>

<!-- Raccourcis -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div style="background:white;border-radius:18px;padding:28px;border:1px solid #f0eaff;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <h3 style="color:#1a0533;font-size:16px;margin-bottom:6px;">📚 Cours</h3>
        <p style="color:#999;font-size:13px;margin-bottom:18px;">Gérez, approuvez ou rejetez les cours soumis par les utilisateurs.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="/SkillSwap/view/admin/liste_cours_admin.php" class="btn btn-primary btn-sm">Voir tous les cours</a>
            <a href="/SkillSwap/view/admin/ajout_cours_admin.php" class="btn btn-ghost btn-sm">+ Nouveau cours</a>
        </div>
    </div>
    <div style="background:white;border-radius:18px;padding:28px;border:1px solid #f0eaff;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <h3 style="color:#1a0533;font-size:16px;margin-bottom:6px;">📋 Demandes</h3>
        <p style="color:#999;font-size:13px;margin-bottom:18px;">Gérez, approuvez ou rejetez les demandes soumises par les utilisateurs.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="/SkillSwap/view/admin/liste_demande_admin.php" class="btn btn-primary btn-sm">Voir toutes les demandes</a>
            <a href="/SkillSwap/view/admin/ajout_demande_admin.php" class="btn btn-ghost btn-sm">+ Nouvelle demande</a>
        </div>
    </div>
</div>

</div></div></body></html>
