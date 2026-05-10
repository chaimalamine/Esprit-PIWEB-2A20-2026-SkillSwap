<?php $base = '/SkillSwap'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f3ff; min-height: 100vh; }

        /* ── Navbar — identique au frontdesign ── */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 48px; height: 68px; background: white;
            box-shadow: 0 1px 0 #ede9ff;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar-brand { display: flex; align-items: center; gap: 9px; text-decoration: none; }
        .navbar-brand .dot { width: 11px; height: 11px; background: #7b2ff7; border-radius: 50%; }
        .navbar-brand span { color: #7b2ff7; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
        .navbar-links { display: flex; align-items: center; gap: 2px; }
        .navbar-links a {
            padding: 8px 18px; text-decoration: none; color: #555;
            font-size: 14px; font-weight: 500; border-radius: 22px;
            transition: background 0.15s, color 0.15s;
        }
        .navbar-links a:hover { background: #f5f3ff; color: #7b2ff7; }
        .navbar-links .btn-start {
            background: linear-gradient(135deg, #7b2ff7, #a855f7);
            color: white !important; margin-left: 8px; font-weight: 600;
            box-shadow: 0 2px 8px rgba(123,47,247,0.25);
        }
        .navbar-links .btn-start:hover { opacity: 0.9; }

        /* ── Content ── */
        .content { padding: 48px 60px; max-width: 1100px; margin: 0 auto; }

        /* ── Page header ── */
        .page-header { margin-bottom: 32px; }
        .page-header h1 { color: #1a0533; font-size: 28px; font-weight: 700; margin-bottom: 6px; }
        .page-header p  { color: #888; font-size: 15px; }
        .page-header .header-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 22px; border-radius: 22px;
            text-decoration: none; font-size: 14px; font-weight: 600;
            cursor: pointer; border: none; transition: all 0.2s; white-space: nowrap;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .btn-purple { background: linear-gradient(135deg, #7b2ff7, #a855f7); color: white; }
        .btn-red    { background: #fee2e2; color: #dc2626; }
        .btn-red:hover { background: #dc2626; color: white; }
        .btn-gray   { background: white; color: #7b2ff7; border: 1.5px solid #e0d9f7; }
        .btn-gray:hover { background: #f5f3ff; }
        .btn-sm     { padding: 6px 14px; font-size: 13px; }

        /* ── Cards ── */
        .card {
            background: white; border-radius: 18px; padding: 24px 28px; margin-bottom: 14px;
            box-shadow: 0 1px 4px rgba(123,47,247,0.05), 0 4px 16px rgba(0,0,0,0.04);
            border: 1px solid #f0eaff; transition: box-shadow 0.2s, transform 0.2s;
        }
        .card:hover { box-shadow: 0 4px 24px rgba(123,47,247,0.1); transform: translateY(-2px); }
        .card h3 { color: #1a0533; font-size: 17px; font-weight: 700; margin-bottom: 5px; }
        .card p  { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
        .card-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
        .card-meta .tag {
            display: inline-flex; align-items: center; gap: 5px;
            background: #f5f3ff; color: #7b2ff7; border-radius: 20px;
            padding: 3px 12px; font-size: 12px; font-weight: 500;
        }
        .card .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        /* Badge */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .badge-pending  { background: #fef9c3; color: #a16207; }
        .badge-approved { background: #dcfce7; color: #15803d; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }

        /* ── Form layout ── */
        .form-wrapper { display: grid; grid-template-columns: 1fr 340px; gap: 32px; align-items: start; }
        .form-card {
            background: white; border-radius: 20px; padding: 40px 36px;
            box-shadow: 0 2px 8px rgba(123,47,247,0.07), 0 8px 32px rgba(0,0,0,0.04);
            border: 1px solid #f0eaff;
        }
        .form-card h2 { color: #1a0533; font-size: 20px; font-weight: 700; margin-bottom: 5px; }
        .form-subtitle { color: #aaa; font-size: 13px; margin-bottom: 26px; display: block; }
        .req { color: #7b2ff7; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 7px; }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%; padding: 12px 16px;
            border: 1.5px solid #e8e2f7; border-radius: 12px;
            font-size: 14px; font-family: inherit; outline: none;
            color: #1a0533; background: #fdfcff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #7b2ff7; box-shadow: 0 0 0 3px rgba(123,47,247,0.08); background: white;
        }
        .form-group textarea { height: 120px; resize: vertical; }
        .form-group .field-msg { display: block; font-size: 12px; margin-top: 5px; min-height: 16px; }
        .form-group input::placeholder, .form-group textarea::placeholder { color: #bbb; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-actions { display: flex; gap: 12px; margin-top: 8px; }

        /* Info panel */
        .info-panel {
            background: linear-gradient(160deg, #7b2ff7, #a855f7);
            border-radius: 20px; padding: 32px 26px; color: white;
        }
        .info-panel h3 { font-size: 17px; font-weight: 700; margin-bottom: 8px; }
        .info-panel p  { font-size: 13px; opacity: 0.85; line-height: 1.6; margin-bottom: 18px; }
        .info-panel ul { list-style: none; }
        .info-panel ul li { font-size: 13px; opacity: 0.9; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.12); }
        .info-panel ul li:last-child { border: none; }
        .info-notice { background: rgba(255,255,255,0.15); border-radius: 12px; padding: 14px 16px; margin-top: 18px; font-size: 13px; line-height: 1.55; }

        /* ── Hero banner (for liste pages) ── */
        .page-hero {
            background: linear-gradient(135deg, #7b2ff7 0%, #a855f7 60%, #c084fc 100%);
            border-radius: 20px; padding: 40px 44px; margin-bottom: 32px;
            color: white; display: flex; align-items: center; justify-content: space-between; gap: 24px;
        }
        .page-hero h1 { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .page-hero p  { font-size: 14px; opacity: 0.85; }
        .btn-white { background: white; color: #7b2ff7; font-weight: 700; }
        .btn-white:hover { background: #f5f3ff; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center; padding: 60px 20px;
            background: white; border-radius: 18px; border: 1px dashed #e0d9f7;
        }
        .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; }
        .empty-state h3 { color: #444; font-size: 18px; margin-bottom: 8px; }
        .empty-state p  { color: #aaa; font-size: 14px; }
    </style>
    <script src="<?= $base ?>/js/skillswap.js" defer></script>
</head>
<body>
<nav class="navbar">
    <a href="<?= $base ?>/index.php" class="navbar-brand">
        <div class="dot"></div>
        <span>SkillSwap</span>
    </a>
    <div class="navbar-links">
        <a href="<?= $base ?>/index.php">Accueil</a>
        <a href="<?= $base ?>/view/explorer.php">Explorer</a>
        <a href="<?= $base ?>/view/cours/ajout_cours.php">Proposer</a>
        <a href="<?= $base ?>/view/cours/liste_cours.php" class="btn-start">Commencer</a>
    </div>
</nav>
<div class="content">
