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

        /* Overrides pour des boutons modernes */
        .crud-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid rgba(0,0,0,0.1);
            background: #ffffff;
            color: #4b5563;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        .crud-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            background: #fcfcfc;
        }
        .crud-btn.danger {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.2);
        }
        .crud-btn.danger:hover {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            box-shadow: 0 6px 16px rgba(244, 63, 94, 0.35);
            transform: translateY(-2px);
        }

        /* Modal avec Glassmorphism */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 31, 22, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            animation: fadeInModal 0.3s ease-out;
        }
        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .custom-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: #ffffff;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 440px;
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #1f2937;
            animation: slideUpModal 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes slideUpModal {
            to { transform: translate(-50%, -50%) scale(1); }
        }
        .custom-modal-icon {
            font-size: 48px;
            margin-bottom: 16px;
            animation: pulseIcon 1.5s infinite ease-in-out;
        }
        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .custom-modal-content h3 {
            font-size: 22px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 12px;
            text-shadow: none;
        }
        .custom-modal-content p {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 24px;
            text-shadow: none;
        }
        .custom-modal-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .custom-modal-btn {
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .custom-modal-btn.cancel {
            background: #f3f4f6;
            color: #4b5563;
        }
        .custom-modal-btn.cancel:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        .custom-modal-btn.confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        .custom-modal-btn.confirm:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
            transform: translateY(-2px);
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
                        <a class="crud-btn" href="<?php echo htmlspecialchars($urlEdit, ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int) $u['id_user']; ?>">✏️ Modifier</a>
                        <button class="crud-btn danger" style="cursor: pointer;" onclick="openDeleteModal('<?php echo htmlspecialchars($urlDelete, ENT_QUOTES, 'UTF-8'); ?>?id=<?php echo (int) $u['id_user']; ?>')">🗑️ Supprimer</button>
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

<!-- Custom Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-icon">⚠️</div>
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ? Cette action est irréversible.</p>
        <div class="custom-modal-actions">
            <button class="custom-modal-btn cancel" onclick="closeDeleteModal()">Annuler</button>
            <a id="confirmDeleteLink" class="custom-modal-btn confirm" href="#">Oui, Supprimer</a>
        </div>
    </div>
</div>

<script>
function openDeleteModal(deleteUrl) {
    const modal = document.getElementById('deleteConfirmModal');
    const confirmLink = document.getElementById('confirmDeleteLink');
    confirmLink.href = deleteUrl;
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('deleteConfirmModal');
    if (e.target === modal) {
        closeDeleteModal();
    }
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>

