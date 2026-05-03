<?php
declare(strict_types=1);

// VIEW — HTML + appels Controller + redirections (PRG). PAS DE SQL.
require_once __DIR__ . '/../controller/utilisateurC.php';
require_once __DIR__ . '/partials/auth.php';
require_admin();

$pageTitle = 'Liste des utilisateurs';
$controller = new UtilisateurC();
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$appBase = (string) preg_replace('#/view/[^/]+$#', '', $scriptName);
$urlListe = $appBase . '/view/liste.php';
$urlAjout = $appBase . '/view/ajout.php';
$urlEdit = $appBase . '/view/edit.php';
$urlDelete = $appBase . '/view/delete.php';
$filtres = [
    'nom' => trim((string) ($_GET['nom'] ?? '')),
    'email' => trim((string) ($_GET['email'] ?? '')),
    'role' => trim((string) ($_GET['role'] ?? '')),
];
$tri = trim((string) ($_GET['tri'] ?? 'nom_asc'));
$liste = $controller->afficher($filtres, $tri);
$stats = $controller->statistiques();
$total = max(1, (int) ($stats['total'] ?? 0));
$pAdmins = round(((int) ($stats['admins'] ?? 0) / $total) * 100, 1);
$pActifs = round(((int) ($stats['utilisateurs_actifs'] ?? 0) / $total) * 100, 1);
$pInactifs = round(((int) ($stats['utilisateurs_inactifs'] ?? 0) / $total) * 100, 1);
$angleAdmins = ($pAdmins / 100) * 360;
$angleActifs = ($pActifs / 100) * 360;
$angleInactifs = 360 - $angleAdmins - $angleActifs;
$pieGradient = sprintf(
    'conic-gradient(#4f46e5 0deg %.2fdeg, #22c55e %.2fdeg %.2fdeg, #f97316 %.2fdeg %.2fdeg)',
    $angleAdmins,
    $angleAdmins,
    $angleAdmins + $angleActifs,
    $angleAdmins + $angleActifs,
    $angleAdmins + $angleActifs + $angleInactifs
);

require __DIR__ . '/partials/header.php';
?>

<div class="crud-card">
    <style>
        .pro-filters {
            background: #ffffff;
            border: 1px solid #d9ebe0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(34, 139, 88, 0.08);
        }
        .pro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }
        .pro-input, .pro-select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #c9ded0;
            border-radius: 8px;
            font-size: 14px;
        }
        .stats-pie-wrap {
            background: #fff;
            border: 1px solid #d9ebe0;
            border-radius: 14px;
            padding: 18px;
            margin: 16px 0 20px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 22px;
            align-items: center;
        }
        .stats-pie {
            width: 280px;
            height: 280px;
            border-radius: 50%;
            margin: 0 auto;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
            position: relative;
        }
        .stats-pie::after {
            content: "";
            position: absolute;
            inset: 68px;
            background: #fff;
            border-radius: 50%;
        }
        .stats-center {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #1f2937;
            z-index: 2;
            text-align: center;
            line-height: 1.2;
        }
        .stats-center small { display: block; font-size: 12px; color: #667085; font-weight: 600; }
        .legend { display: grid; gap: 10px; }
        .legend-item { display:flex; align-items:center; gap:10px; font-size:14px; }
        .dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
        .dot-admin { background:#4f46e5; }
        .dot-active { background:#22c55e; }
        .dot-inactive { background:#f97316; }
        @media (max-width: 900px) {
            .stats-pie-wrap { grid-template-columns: 1fr; }
        }
    </style>

    <h2 style="color:#1b5e20;margin-bottom:10px;">Liste des utilisateurs</h2>

    <form method="get" class="pro-filters">
        <strong style="color:#1b5e20;">Recherche et tri avancés</strong>
        <div class="pro-grid">
            <div>
                <label for="nom">Nom</label>
                <input class="pro-input" id="nom" name="nom" type="text" placeholder="Nom ou prénom"
                       value="<?php echo htmlspecialchars($filtres['nom'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label for="email">Email</label>
                <input class="pro-input" id="email" name="email" type="text" placeholder="Adresse email"
                       value="<?php echo htmlspecialchars($filtres['email'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label for="role">Rôle</label>
                <select class="pro-select" id="role" name="role">
                    <option value="">Tous</option>
                    <option value="utilisateur" <?php echo $filtres['role'] === 'utilisateur' ? 'selected' : ''; ?>>Utilisateur</option>
                    <option value="admin" <?php echo $filtres['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div>
                <label for="tri">Tri</label>
                <select class="pro-select" id="tri" name="tri">
                    <option value="nom_asc" <?php echo $tri === 'nom_asc' ? 'selected' : ''; ?>>Nom (A-Z)</option>
                    <option value="email_asc" <?php echo $tri === 'email_asc' ? 'selected' : ''; ?>>Email (A-Z)</option>
                    <option value="role_asc" <?php echo $tri === 'role_asc' ? 'selected' : ''; ?>>Rôle</option>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px;">
            <button class="crud-btn primary" type="submit">Appliquer</button>
            <a class="crud-btn" href="<?php echo htmlspecialchars($urlListe, ENT_QUOTES, 'UTF-8'); ?>">Réinitialiser</a>
        </div>
    </form>

    <div class="stats-pie-wrap">
        <div class="stats-pie" style="background: <?php echo htmlspecialchars($pieGradient, ENT_QUOTES, 'UTF-8'); ?>;">
            <div class="stats-center">
                <?php echo (int) $stats['total']; ?>
                <small>Utilisateurs</small>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="dot dot-admin"></span>
                <span>Administrateurs: <?php echo (int) $stats['admins']; ?> (<?php echo number_format($pAdmins, 1); ?>%)</span>
            </div>
            <div class="legend-item">
                <span class="dot dot-active"></span>
                <span>Utilisateurs actifs: <?php echo (int) $stats['utilisateurs_actifs']; ?> (<?php echo number_format($pActifs, 1); ?>%)</span>
            </div>
            <div class="legend-item">
                <span class="dot dot-inactive"></span>
                <span>Utilisateurs inactifs: <?php echo (int) $stats['utilisateurs_inactifs']; ?> (<?php echo number_format($pInactifs, 1); ?>%)</span>
            </div>
            <div style="margin-top:6px;color:#667085;font-size:13px;">
                Répartition en pourcentage des catégories d'utilisateurs.
            </div>
        </div>
    </div>

    <div style="overflow:auto;">
        <table class="crud-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actif</th>
                <th>Date</th>
                <th style="text-align:center;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($liste as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string) $u['id_user'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) $u['nom_prenom'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) $u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) $u['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo ((int) $u['est_actif']) === 1 ? 'Oui' : 'Non'; ?></td>
                    <td><?php echo htmlspecialchars((string) $u['date_creation'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td style="text-align:center;white-space:nowrap;">
                        <a class="crud-btn" href="<?php echo htmlspecialchars($urlEdit, ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int) $u['id_user']; ?>">Modifier</a>
                        <a class="crud-btn danger" href="<?php echo htmlspecialchars($urlDelete, ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int) $u['id_user']; ?>"
                           onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($liste === []): ?>
                <tr><td colspan="7" style="text-align:center;color:#777;">Aucun utilisateur.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

