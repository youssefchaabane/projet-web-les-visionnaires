<?php
declare(strict_types=1);
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!function_exists('h')) {
    function h(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/../controller/allergiecontroller.php';
require_once __DIR__ . '/../controller/traitementcontroller.php';

$active = 'associations';

$ac = AllergieController::getInstance();
$tc = TraitementController::getInstance();
extract($ac->traiterRequetePageAssociationForm($tc), EXTR_OVERWRITE);

$pageTitle = 'Créer une Association';
require __DIR__ . '/partials/header.php';
?>

<style>
    .admin-sub-nav {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }
    .sub-nav-btn {
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        background: rgba(255,255,255,0.2);
        color: #ffffff;
        border: 1px solid rgba(255,255,255,0.3);
        transition: all 0.25s ease;
    }
    .sub-nav-btn:hover, .sub-nav-btn.active {
        background: #ffffff;
        color: #065f46;
        border-color: #ffffff;
    }
    .crud-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(178, 242, 187, 0.25);
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        color: #1f2937;
        margin-bottom: 24px;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
    }
    .crud-card h1 {
        color: #065f46;
        font-size: 22px;
        margin: 0 0 16px;
        text-shadow: none;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 12px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #ffffff;
        color: #1f2937;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .form-group select:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
    }
    .crud-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 24px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        border: 1px solid rgba(0,0,0,0.1);
        background: #ffffff;
        color: #374151;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .crud-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.06);
        background: #fdfdfd;
        border-color: #10b981;
    }
    .crud-btn.primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
    }
    .crud-btn.primary:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.25);
    }
    .msg {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .msg-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .msg-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .msg-error ul {
        margin: 6px 0 0 16px;
        padding: 0;
    }
    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fee2e2;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: none;
        font-size: 13px;
    }
    .alert-error ul {
        margin: 6px 0 0 16px;
        padding: 0;
    }
    #ai-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #d1fae5;
        color: #065f46;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 12px;
        margin-top: 8px;
        align-self: flex-start;
        animation: pulse 1.5s infinite ease-in-out;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>

<div class="admin-sub-nav">
    <a class="sub-nav-btn" href="allergier_admin.php">🏠 Tableau de bord</a>
    <a class="sub-nav-btn" href="allergies.php">🌿 Allergies</a>
    <a class="sub-nav-btn" href="traitements.php">💊 Traitements</a>
    <a class="sub-nav-btn active" href="associations.php">🔗 Associations</a>
    <a class="sub-nav-btn" href="allergier_admin.php?page=statistiques">📊 Statistiques</a>
</div>

<div class="crud-card">
    <h1>Nouvelle association</h1>
    <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Associez une allergie enregistrée à son traitement adapté. L'IA de Groq sélectionnera automatiquement le traitement le plus pertinent lors du choix de l'allergie.</p>

    <?php if ($message !== ''): ?><div class="msg msg-success"><?php echo h($message); ?></div><?php endif; ?>
    <?php if ($erreurs): ?>
        <div class="msg msg-error"><ul><?php foreach ($erreurs as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form class="allergier-form" data-form-type="association" method="post" action="association_form.php" novalidate>
        <div class="allergier-form-errors alert-error" role="alert"></div>

        <div class="form-group">
            <label for="id_allergie">Allergie ciblée *</label>
            <select id="id_allergie" name="id_allergie" required>
                <option value="">— Sélectionner une allergie —</option>
                <?php foreach ($allergies as $a): ?>
                    <option value="<?php echo (int) ($a['id_allergie'] ?? 0); ?>" <?php echo (string)($ancien['id_allergie'] ?? '') === (string)($a['id_allergie'] ?? '') ? 'selected' : ''; ?>>
                        <?php echo h((string) ($a['nom'] ?? '')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group" style="position: relative;">
            <label for="id_traitement">Traitement recommandé *</label>
            <select id="id_traitement" name="id_traitement" required>
                <option value="">— Sélectionner un traitement —</option>
                <?php foreach ($traitements as $t): ?>
                    <option value="<?php echo (int) ($t['id_traitement'] ?? 0); ?>" <?php echo (string)($ancien['id_traitement'] ?? '') === (string)($t['id_traitement'] ?? '') ? 'selected' : ''; ?>>
                        <?php echo h((string) ($t['nom'] ?? '')); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a class="crud-btn" href="associations.php">Annuler</a>
            <button type="submit" class="crud-btn primary">🔗 Créer l'association</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const allergieSelect = document.getElementById('id_allergie');
    const traitementSelect = document.getElementById('id_traitement');
    const btnSubmit = document.querySelector('button[type="submit"]');

    if (allergieSelect && traitementSelect) {
        allergieSelect.addEventListener('change', function() {
            const idAllergie = this.value;
            if (!idAllergie) return;

            const originalText = btnSubmit.textContent;
            btnSubmit.textContent = "⚡ Analyse par l'IA Groq...";
            btnSubmit.disabled = true;

            fetch('../controller/ajax_ai_suggest.php?id_allergie=' + encodeURIComponent(idAllergie))
                .then(response => response.json())
                .then(data => {
                    if (data.id_traitement) {
                        traitementSelect.value = data.id_traitement;
                        
                        // Add a small badge or indication
                        let aiBadge = document.getElementById('ai-badge');
                        if (!aiBadge) {
                            aiBadge = document.createElement('span');
                            aiBadge.id = 'ai-badge';
                            aiBadge.innerHTML = '✨ Suggéré par l\'IA';
                            traitementSelect.parentNode.appendChild(aiBadge);
                            
                            // Remove after 3s
                            setTimeout(() => {
                                aiBadge.remove();
                            }, 4000);
                        }
                    }
                })
                .catch(err => console.error("Erreur IA: ", err))
                .finally(() => {
                    btnSubmit.textContent = originalText;
                    btnSubmit.disabled = false;
                });
        });
    }
});
</script>

<script src="../assets/js/allergier.js" defer></script>

<?php require __DIR__ . '/partials/footer.php'; ?>
