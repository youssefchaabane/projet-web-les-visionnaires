<?php

declare(strict_types=1);



/**

 * Header ECOSAVE — version "LOGIQUE PROF" (views dans /view)

 * Design identique (couleurs/layout), liens adaptés.

 *

 * Variables optionnelles:

 * - $pageTitle (string)

 */

$pageTitle = $pageTitle ?? 'ECOSAVE';

require_once __DIR__ . '/auth.php';

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

$appBase = (string) preg_replace('#/view/[^/]+$#', '', $scriptName);

$urlAccueil = $appBase . '/view/accueil.php';

$urlUserHome = $appBase . '/view/user_home.php';

$urlListe = $appBase . '/view/liste.php';

$urlAjout = $appBase . '/view/ajout.php';

$urlStatistiques = $appBase . '/view/statistiques.php';

$urlLogout = $appBase . '/view/logout.php';

$urlPlaceholder = 'javascript:void(0)';

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

?>

<!DOCTYPE html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars((string) $pageTitle, ENT_QUOTES, 'UTF-8'); ?> — ECOSAVE</title>

    <link rel="stylesheet" href="../assets/css/client.css">

    <link rel="stylesheet" href="../style.css">

    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {

            font-family: 'Segoe UI', Roboto, sans-serif;

            color: #ffffff !important;

            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);

            min-height: 100vh;

            overflow-x: hidden;

            position: relative;

        }

        body::before {

            content: "";

            position: fixed;

            inset: 0;

            z-index: -2;

            background: <?php echo $isAdmin ? "url('../assets/css/Cheese and Sweetcorn Biscuits - Baby Led Kitchen.jpg') center center / cover fixed no-repeat" : "url('../assets/css/Group of vegetables and fruits on.jpg') center center / cover fixed no-repeat"; ?>;

            filter: <?php echo $isAdmin ? 'brightness(0.3)' : 'none'; ?>;

        }

        body.public::after {

            content: "";

            position: fixed;

            inset: 0;

            z-index: -1;

            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);

        }

        .fo-wrap { display: flex; min-height: 100vh; flex-direction: column; }

        .fo-top {

            background: rgba(0, 0, 0, 0.28);

            backdrop-filter: blur(18px);

            color: #fff;

            padding: 16px 24px;

            display: flex;

            flex-wrap: wrap;

            align-items: center;

            justify-content: space-between;

            gap: 12px;

            border-bottom: 1px solid rgba(255, 255, 255, 0.08);

        }

        .fo-top .brand { font-size: 22px; font-weight: bold; }

        .fo-top .brand a { color: #ffffff; text-decoration: none; }

        .fo-top nav { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

        .fo-top nav a {

            color: #ffffff;

            text-decoration: none;

            padding: 8px 15px;

            border-radius: 30px;

            background: rgba(255, 255, 255, 0.08);

            border: 1px solid rgba(255,255,255,0.3);

            font-size: 14px;

            transition: all 0.3s ease;

        }

        .fo-top nav a:hover { background: rgba(255, 255, 255, 0.12); color: #ffffff; transform: translateY(-2px); }

        .fo-top nav a.disabled {

            background: rgba(178, 242, 187, 0.05);

            opacity: 0.72;

            cursor: not-allowed;

        }

        .fo-body { display: flex; flex: 1; max-width: 1400px; width: 100%; margin: 0 auto; }

        .fo-sidebar {

            width: 60px;

            flex-shrink: 0;

            background: rgba(10, 61, 42, 0.95);

            backdrop-filter: blur(15px);

            border-right: 1px solid rgba(178, 242, 187, 0.2);

            padding: 16px 0;

            transition: width 0.3s ease;

        }

        .fo-sidebar:hover { width: 200px; }

        .fo-sidebar .sec { font-size: 10px; text-transform: uppercase; color: #b2f2bb; padding: 8px 10px; font-weight: bold; opacity: 0; transition: opacity 0.3s; }

        .fo-sidebar:hover .sec { opacity: 1; }

        .fo-sidebar a {

            display: flex;

            align-items: center;

            padding: 12px 10px;

            color: #b2f2bb;

            text-decoration: none;

            font-size: 14px;

            border-left: 3px solid transparent;

            transition: all 0.3s ease;

            white-space: nowrap;

        }

        .fo-sidebar a:hover { background: rgba(178, 242, 187, 0.1); border-left-color: #b2f2bb; transform: translateX(5px); }

        .fo-sidebar a.active { background: rgba(178, 242, 187, 0.2); border-left-color: #b2f2bb; color: #fff; font-weight: 600; }

        .fo-sidebar a.disabled { opacity: 0.6; pointer-events: none; }

        .fo-sidebar a span { margin-right: 10px; font-size: 18px; }

        .fo-sidebar a .text { opacity: 0; transition: opacity 0.3s; }

        .fo-sidebar:hover a .text { opacity: 1; }

        

        /* Language Selector Styles */

        .language-selector {

            display: flex;

            gap: 8px;

            margin-left: auto;

            align-items: center;

        }

        .language-btn {

            display: flex;

            align-items: center;

            gap: 6px;

            padding: 6px 10px;

            background: rgba(255, 255, 255, 0.08);

            border: 1px solid rgba(255, 255, 255, 0.2);

            border-radius: 20px;

            color: #ffffff;

            cursor: pointer;

            transition: all 0.3s ease;

            font-size: 12px;

        }

        .language-btn:hover {

            background: rgba(255, 255, 255, 0.12);

            transform: translateY(-2px);

        }

        .language-btn.active {

            background: #b2f2bb;

            color: #0a3d2a;

        }

        .flag {

            font-size: 16px;

        }

        .lang-name {

            font-size: 11px;

            font-weight: 500;

        }

        .fo-main { flex: 1; padding: 28px 24px; min-width: 0; }

        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-top: 18px; }

        .dashboard-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 18px; border-radius: 14px; box-shadow: 0 12px 18px rgba(0,0,0,0.18); transition: transform 0.3s ease, box-shadow 0.3s ease; color: #fff; }

        .dashboard-card:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 20px 30px rgba(0,0,0,0.28); }

        .dashboard-card h3 { margin-bottom: 10px; font-size: 16px; color: #ffffff; }

        .dashboard-card strong { display: block; font-size: 32px; margin-top: 6px; color: #fff; }

        .dashboard-tabs { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }

        .dashboard-tab { border: none; background: rgba(178, 242, 187, 0.1); color: #b2f2bb; padding: 10px 18px; border-radius: 999px; cursor: pointer; transition: background 0.3s ease; }

        .dashboard-tab.active, .dashboard-tab:hover { background: #b2f2bb; color: #0a3d2a; }

        .dashboard-panel { display: none; margin-top: 18px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 18px; box-shadow: 0 10px 18px rgba(0,0,0,0.1); color: #fff; }

        .dashboard-panel.active { display: block; }

        .dashboard-panel h3 { margin-bottom: 12px; color: #b2f2bb; }

        .dashboard-panel p { color: #e0e0e0; line-height: 1.6; }

        .dashboard-panel input, .dashboard-panel select { width: 100%; padding: 10px 12px; border: 1px solid rgba(178, 242, 187, 0.3); border-radius: 10px; margin-bottom: 12px; background: rgba(255, 255, 255, 0.1); color: #fff; }

        .dashboard-panel table { width: 100%; border-collapse: collapse; margin-top: 12px; }

        .dashboard-panel th, .dashboard-panel td { padding: 10px 12px; border-bottom: 1px solid rgba(178, 242, 187, 0.2); text-align: left; color: #fff; }

        .dashboard-panel th { background: rgba(178, 242, 187, 0.1); color: #b2f2bb; }

        .pulse { animation: pulse 2.2s ease-in-out infinite alternate; }

        @keyframes pulse { from { transform: translateY(0); } to { transform: translateY(-5px) scale(1.01); } }

        @media (max-width: 900px) {

            .fo-body { flex-direction: column; }

            .fo-sidebar { width: 100%; border-right: none; border-bottom: 1px solid rgba(178, 242, 187, 0.2); }

        }

    </style>

<script src="../assets/js/language.js"></script>

</head>

<body class="<?php echo $isAdmin ? 'admin' : 'public'; ?>">

<div class="fo-wrap">

    <div class="fo-top">

        <div class="brand"><a href="<?php echo htmlspecialchars($isAdmin ? $urlAccueil : $urlUserHome, ENT_QUOTES, 'UTF-8'); ?>">🌱 ECOSAVE</a></div>

        <nav>

            <a href="<?php echo htmlspecialchars($isAdmin ? $urlAccueil : $urlUserHome, ENT_QUOTES, 'UTF-8'); ?>" data-translate="accueil">🏠 Accueil</a>

            

            <!-- Language Selector -->

            <div class="language-selector">

                <button class="language-btn" onclick="changeLanguage('fr')">
                    <span class="lang-name">Français</span>
                </button>

                
                <button class="language-btn" onclick="changeLanguage('en')">
                    <span class="lang-name">English</span>
                </button>

            </div>

            

            <a href="<?php echo htmlspecialchars($urlLogout, ENT_QUOTES, 'UTF-8'); ?>" data-translate="deconnexion">Déconnexion</a>

        </nav>

    </div>

    <div class="fo-body">

        <div class="fo-sidebar">

            <div class="sec" data-translate="navigation">Navigation</div>

            <?php if ($isAdmin): ?>

                <a class="<?php echo basename($_SERVER['SCRIPT_NAME'] ?? '') === 'liste.php' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($urlListe, ENT_QUOTES, 'UTF-8'); ?>"><span>👥</span><span class="text" data-translate="liste">Liste</span></a>

                <a class="<?php echo basename($_SERVER['SCRIPT_NAME'] ?? '') === 'ajout.php' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($urlAjout, ENT_QUOTES, 'UTF-8'); ?>"><span>➕</span><span class="text" data-translate="ajout">Ajout</span></a>

                <div class="sec" data-translate="modules">Modules</div>

                <a class="<?php echo basename($_SERVER['SCRIPT_NAME'] ?? '') === 'liste.php' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($urlListe, ENT_QUOTES, 'UTF-8'); ?>"><span>👥</span><span class="text" data-translate="utilisateurs">Utilisateurs</span></a>

                <a class="disabled" href="<?php echo htmlspecialchars($urlPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><span>📦</span><span class="text" data-translate="stock">Stock</span></a>

                <a class="disabled" href="<?php echo htmlspecialchars($urlPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><span>🤧</span><span class="text" data-translate="allergies">Allergies</span></a>

                <a class="disabled" href="<?php echo htmlspecialchars($urlPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><span>📰</span><span class="text" data-translate="publication">Publication</span></a>

                <a class="disabled" href="<?php echo htmlspecialchars($urlPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><span>🍽️</span><span class="text" data-translate="recettes">Recettes</span></a>

                <a class="disabled" href="<?php echo htmlspecialchars($urlPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"><span>🌍</span><span class="text" data-translate="empreinte">Empreinte</span></a>

                            <?php else: ?>

                <a class="<?php echo basename($_SERVER['SCRIPT_NAME'] ?? '') === 'user_home.php' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($urlUserHome, ENT_QUOTES, 'UTF-8'); ?>"><span>🏠</span><span class="text">Mon espace</span></a>

            <?php endif; ?>

        </div>

        <div class="fo-main">



