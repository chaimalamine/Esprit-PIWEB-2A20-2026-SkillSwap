<?php $base = '/SkillSwap'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap — Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; min-height: 100vh; display: flex; }

        /* ── Sidebar ── */
        .sidebar {
            width: 248px; min-height: 100vh; background: white;
            box-shadow: 1px 0 0 #ede9ff;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; z-index: 100;
        }
        .sidebar-brand { padding: 24px 22px 18px; border-bottom: 1px solid #f0eaff; }
        .brand-row { display: flex; align-items: center; gap: 9px; margin-bottom: 2px; }
        .brand-dot { width: 10px; height: 10px; background: #7b2ff7; border-radius: 50%; }
        .brand-name { color: #7b2ff7; font-size: 19px; font-weight: 700; }
        .brand-tag { font-size: 11px; color: #c4b5fd; letter-spacing: 1.2px; text-transform: uppercase; font-weight: 500; }

        .sidebar nav { flex: 1; padding: 12px 0; overflow-y: auto; }
        .nav-label { padding: 14px 22px 5px; font-size: 10px; text-transform: uppercase; letter-spacing: 1.4px; color: #d1c4e9; font-weight: 700; }

        .nav-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 22px; text-decoration: none; color: #6b7280;
            font-size: 14px; font-weight: 500;
            transition: all 0.15s; border-left: 3px solid transparent;
        }
        .nav-item:hover { background: #faf8ff; color: #7b2ff7; border-left-color: #e9d5ff; }
        .nav-item.active { background: #f5f0ff; color: #7b2ff7; border-left-color: #7b2ff7; font-weight: 600; }
        .nav-icon { width: 18px; height: 18px; flex-shrink: 0; }

        .nav-sub {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 22px 8px 44px; text-decoration: none; color: #9ca3af;
            font-size: 13px; transition: all 0.15s; border-left: 3px solid transparent;
        }
        .nav-sub:hover { background: #faf8ff; color: #7b2ff7; }
        .nav-sub.active { color: #7b2ff7; background: #f5f0ff; border-left-color: #7b2ff7; font-weight: 600; }
        .nav-sub::before { content: ''; width: 5px; height: 5px; background: currentColor; border-radius: 50%; flex-shrink: 0; }

        .sidebar-footer { padding: 16px 22px; border-top: 1px solid #f0eaff; }
        .sidebar-footer a { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #9ca3af; font-size: 13px; transition: color 0.15s; }
        .sidebar-footer a:hover { color: #7b2ff7; }

        /* ── Main ── */
        .main { margin-left: 248px; flex: 1; min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            background: white; padding: 0 36px; height: 60px;
            border-bottom: 1px solid #f0eaff;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { color: #1a0533; font-size: 15px; font-weight: 600; }
        .admin-badge {
            background: #f5f0ff; color: #7b2ff7; padding: 5px 14px;
            border-radius: 20px; font-size: 13px; font-weight: 600;
            border: 1px solid #e9d5ff;
        }

        .content { padding: 32px 36px; }

        /* ── Buttons — Pro, no emoji ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 20px; border-radius: 8px;
            text-decoration: none; font-size: 13px; font-weight: 600;
            cursor: pointer; border: none; transition: all 0.18s; white-space: nowrap;
            letter-spacing: 0.1px;
        }
        .btn:hover { transform: translateY(-1px); }

        /* Primary */
        .btn-primary {
            background: #7b2ff7; color: white;
            box-shadow: 0 1px 3px rgba(123,47,247,0.3);
        }
        .btn-primary:hover { background: #6920d4; box-shadow: 0 4px 12px rgba(123,47,247,0.35); }

        /* Success */
        .btn-success {
            background: white; color: #16a34a;
            border: 1.5px solid #bbf7d0;
        }
        .btn-success:hover { background: #16a34a; color: white; border-color: #16a34a; box-shadow: 0 3px 10px rgba(22,163,74,0.25); }

        /* Danger */
        .btn-danger {
            background: white; color: #dc2626;
            border: 1.5px solid #fecaca;
        }
        .btn-danger:hover { background: #dc2626; color: white; border-color: #dc2626; box-shadow: 0 3px 10px rgba(220,38,38,0.25); }

        /* Secondary */
        .btn-secondary {
            background: white; color: #4b5563;
            border: 1.5px solid #e5e7eb;
        }
        .btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; }

        /* Ghost purple */
        .btn-ghost {
            background: #f5f0ff; color: #7b2ff7;
            border: 1.5px solid #e9d5ff;
        }
        .btn-ghost:hover { background: #ede9fe; border-color: #c4b5fd; }

        /* Disabled state (statut déjà appliqué) */
        .btn-disabled {
            background: #f9fafb; color: #9ca3af;
            border: 1.5px solid #e5e7eb;
            cursor: default; pointer-events: none;
            transform: none !important; box-shadow: none !important;
        }

        .btn-sm { padding: 6px 14px; font-size: 12px; }
        .btn-icon { padding: 7px 10px; }

        /* ── Card list ── */
        .card {
            background: white; border-radius: 14px;
            padding: 20px 24px; margin-bottom: 12px;
            border: 1px solid #f0eaff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            display: flex; align-items: flex-start; justify-content: space-between; gap: 20px;
            transition: box-shadow 0.2s;
        }
        .card:hover { box-shadow: 0 4px 16px rgba(123,47,247,0.08); }
        .card-body { flex: 1; min-width: 0; }
        .card-body h3 { color: #1a0533; font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .card-body p  { color: #6b7280; font-size: 13px; line-height: 1.5; margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-meta { font-size: 12px; color: #9ca3af; display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }
        .card-actions { display: flex; flex-direction: column; gap: 7px; align-items: flex-end; flex-shrink: 0; }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
            letter-spacing: 0.2px; text-transform: uppercase;
        }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-pending  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* ── Stats ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 28px; }
        .stat-card {
            background: white; border-radius: 12px; padding: 20px 22px;
            border: 1px solid #f0eaff; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-num   { font-size: 32px; font-weight: 700; color: #7b2ff7; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 13px; color: #9ca3af; }

        /* ── Form layout ── */
        .form-wrapper { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }
        .form-card {
            background: white; border-radius: 16px; padding: 32px 30px;
            border: 1px solid #f0eaff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-card h2 { color: #1a0533; font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .form-subtitle { color: #9ca3af; font-size: 13px; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .req { color: #7b2ff7; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: 14px; font-family: inherit; outline: none;
            color: #111827; background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #7b2ff7; box-shadow: 0 0 0 3px rgba(123,47,247,0.08);
        }
        .form-group textarea { height: 110px; resize: vertical; }
        .form-group .field-msg { display: block; font-size: 12px; margin-top: 4px; min-height: 16px; }
        .form-group input::placeholder, .form-group textarea::placeholder { color: #d1d5db; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-actions { display: flex; gap: 10px; margin-top: 6px; }

        /* Info panel */
        .info-panel {
            background: #1a0533; border-radius: 16px; padding: 28px 24px; color: white;
        }
        .info-panel h3 { font-size: 15px; font-weight: 700; margin-bottom: 7px; color: #e9d5ff; }
        .info-panel p  { font-size: 13px; color: #9ca3af; line-height: 1.55; margin-bottom: 16px; }
        .info-panel ul { list-style: none; }
        .info-panel ul li { font-size: 13px; color: #d1d5db; padding: 7px 0; border-bottom: 1px solid #2d1a4a; display: flex; align-items: center; gap: 9px; }
        .info-panel ul li:last-child { border: none; }
        .info-panel ul li::before { content: ''; width: 4px; height: 4px; background: #7b2ff7; border-radius: 50%; flex-shrink: 0; }
        .info-notice { background: #2d1a4a; border-radius: 10px; padding: 13px 15px; margin-top: 16px; font-size: 13px; color: #c4b5fd; line-height: 1.5; border-left: 3px solid #7b2ff7; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center; padding: 56px 20px;
            background: white; border-radius: 14px; border: 1px dashed #e5e7eb;
        }
        .empty-icon { font-size: 40px; margin-bottom: 12px; }
        .empty-state h3 { color: #374151; font-size: 16px; font-weight: 600; margin-bottom: 6px; }
        .empty-state p  { color: #9ca3af; font-size: 14px; }

        /* SVG icons inline */
        .ico { width: 14px; height: 14px; vertical-align: middle; }
    </style>
    <script src="<?= $base ?>/js/skillswap.js" defer></script>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-row">
            <div class="brand-dot"></div>
            <span class="brand-name">SkillSwap</span>
        </div>
        <div class="brand-tag">Administration</div>
    </div>

    <nav>
        <div class="nav-label">Général</div>
        <a href="<?= $base ?>/view/admin/dashboard.php"
           class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Vue d'ensemble
        </a>

        <div class="nav-label">Offres (Cours)</div>
        <a href="<?= $base ?>/view/admin/liste_cours_admin.php"
           class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'liste_cours_admin.php' ? 'active' : '' ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Liste des cours
        </a>
        <a href="<?= $base ?>/view/admin/ajout_cours_admin.php"
           class="nav-sub <?= basename($_SERVER['PHP_SELF']) === 'ajout_cours_admin.php' ? 'active' : '' ?>">
            Ajouter un cours
        </a>

        <div class="nav-label">Demandes</div>
        <a href="<?= $base ?>/view/admin/liste_demande_admin.php"
           class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'liste_demande_admin.php' ? 'active' : '' ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><path d="M9 12h6M9 16h4"/></svg>
            Liste des demandes
        </a>
        <a href="<?= $base ?>/view/admin/ajout_demande_admin.php"
           class="nav-sub <?= basename($_SERVER['PHP_SELF']) === 'ajout_demande_admin.php' ? 'active' : '' ?>">
            Ajouter une demande
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= $base ?>/index.php">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retour au site
        </a>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <div class="topbar">
        <span class="topbar-title"><?php
            $titles = [
                'dashboard.php'            => 'Vue d\'ensemble',
                'liste_cours_admin.php'    => 'Gestion des cours',
                'ajout_cours_admin.php'    => 'Ajouter un cours',
                'update_cours_admin.php'   => 'Modifier un cours',
                'liste_demande_admin.php'  => 'Gestion des demandes',
                'ajout_demande_admin.php'  => 'Ajouter une demande',
                'update_demande_admin.php' => 'Modifier une demande',
            ];
            echo $titles[basename($_SERVER['PHP_SELF'])] ?? 'Administration';
        ?></span>
        <span class="admin-badge">Administrateur</span>
    </div>
    <div class="content">
