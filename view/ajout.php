<?php
declare(strict_types=1);

// VIEW — HTML + appels Controller + redirections (PRG). PAS DE SQL.
require_once __DIR__ . '/../controller/utilisateurC.php';
require_once __DIR__ . '/partials/auth.php';
require_admin();

$pageTitle = 'Ajouter un utilisateur';
$controller = new UtilisateurC();
$erreur = '';
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$appBase = (string) preg_replace('#/view/[^/]+$#', '', $scriptName);
$urlListe = $appBase . '/view/liste.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = (string) ($_POST['nom_prenom'] ?? '');
    $email = (string) ($_POST['email'] ?? '');
    $mdp = (string) ($_POST['mot_de_passe'] ?? '');
    $role = (string) ($_POST['role'] ?? 'utilisateur');
    $actif = isset($_POST['est_actif']) ? 1 : 0;
    $niv = (string) ($_POST['niveau_activite'] ?? '');
    $regime = (string) ($_POST['regime_alimentaire'] ?? '');
    $objS = (string) ($_POST['objectif_sante'] ?? '');
    $objE = (string) ($_POST['objectif_eco'] ?? '');

    $nom = trim($nom);
    $email = trim($email);
    $mdp = (string) $mdp;
    $role = trim($role);
    $niv = trim($niv);
    $regime = trim($regime);
    $objS = trim($objS);
    $objE = trim($objE);
    $regimesAutorises = ['', 'omnivore', 'vegetarien', 'vegan', 'pescetarien', 'sans_gluten', 'sans_lactose'];
    $objectifsEcoAutorises = ['', 'reduire_dechets', 'reduire_plastique', 'manger_local', 'reduire_carbone', 'anti_gaspillage'];

    // Contrôle de saisie (VIEW) — la table impose des limites (ex: nom<=150, email<=190, etc.)
    if ($nom === '' || $email === '' || trim($mdp) === '') {
        $erreur = 'Nom, email et mot de passe sont obligatoires.';
    } elseif (mb_strlen($nom) < 2 || mb_strlen($nom) > 150) {
        $erreur = 'Le nom doit contenir entre 2 et 150 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse e-mail invalide.';
    } elseif (mb_strlen($email) > 190) {
        $erreur = "L'e-mail est trop long (max 190 caractères).";
    } elseif (mb_strlen($mdp) < 8) {
        $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!in_array($role, ['utilisateur', 'admin'], true)) {
        $erreur = 'Rôle invalide.';
    } elseif ($niv !== '' && !in_array($niv, ['', 'sédentaire', 'léger', 'modéré', 'intense'], true)) {
        $erreur = 'Niveau d’activité invalide.';
    } elseif (!in_array($regime, $regimesAutorises, true)) {
        $erreur = 'Régime alimentaire invalide.';
    } elseif (!in_array($objE, $objectifsEcoAutorises, true)) {
        $erreur = 'Objectif éco invalide.';
    } elseif (mb_strlen($regime) > 120 || mb_strlen($objS) > 120 || mb_strlen($objE) > 120) {
        $erreur = 'Certains champs dépassent la longueur maximale (120 caractères).';
    } else {
        $u = new UtilisateurEntity();
        $u->setNom_prenom($nom);
        $u->setEmail($email);
        $u->setMot_de_passe($mdp);
        $u->setRole($role);
        $u->setEst_actif($actif);
        $u->setNiveau_activite($niv);
        $u->setRegime_alimentaire($regime);
        $u->setObjectif_sante($objS);
        $u->setObjectif_eco($objE);

        $controller->ajouter($u);
        header('Location: ' . $urlListe);
        exit;
    }
}

require __DIR__ . '/partials/header.php';
?>

<div class="crud-card">
    <h2 style="color:#1b5e20;margin-bottom:10px;">Ajouter un utilisateur</h2>
    <?php if ($erreur !== ''): ?>
        <div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:10px 12px;border-radius:8px;margin:10px 0;">
            <?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form class="crud-form" id="form-ajout" method="post" action="" novalidate>
        <label>Nom & Prénom</label>
        <input type="text" name="nom_prenom" maxlength="150"
               value="<?php echo htmlspecialchars((string) ($_POST['nom_prenom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="field-error" data-field-error="nom_prenom" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Email</label>
        <input type="text" name="email" maxlength="190"
               value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="field-error" data-field-error="email" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Mot de passe</label>
        <input type="text" name="mot_de_passe">
        <span class="field-error" data-field-error="mot_de_passe" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Niveau d'activité</label>
        <select name="niveau_activite">
            <?php
            $niv = (string) ($_POST['niveau_activite'] ?? '');
            foreach (['', 'sédentaire', 'léger', 'modéré', 'intense'] as $opt):
            ?>
                <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $opt === $niv ? 'selected' : ''; ?>>
                    <?php echo $opt === '' ? '—' : htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="field-error" data-field-error="niveau_activite" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
        
        <label>Régime alimentaire</label>
        <?php $regime = (string) ($_POST['regime_alimentaire'] ?? ''); ?>
        <select name="regime_alimentaire">
            <?php
            $optionsRegime = [
                '' => '—',
                'omnivore' => 'Omnivore',
                'vegetarien' => 'Végétarien',
                'vegan' => 'Vegan',
                'pescetarien' => 'Pescétarien',
                'sans_gluten' => 'Sans gluten',
                'sans_lactose' => 'Sans lactose',
            ];
            foreach ($optionsRegime as $valeur => $libelle):
            ?>
                <option value="<?php echo htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $regime === $valeur ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="field-error" data-field-error="regime_alimentaire" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Objectif santé</label>
        <input type="text" name="objectif_sante" maxlength="120"
               value="<?php echo htmlspecialchars((string) ($_POST['objectif_sante'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="field-error" data-field-error="objectif_sante" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Objectif éco</label>
        <?php $objE = (string) ($_POST['objectif_eco'] ?? ''); ?>
        <select name="objectif_eco">
            <?php
            $optionsEco = [
                '' => '—',
                'reduire_dechets' => 'Réduire les déchets',
                'reduire_plastique' => 'Réduire le plastique',
                'manger_local' => 'Manger local',
                'reduire_carbone' => 'Réduire l\'empreinte carbone',
                'anti_gaspillage' => 'Lutter contre le gaspillage',
            ];
            foreach ($optionsEco as $valeur => $libelle):
            ?>
                <option value="<?php echo htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $objE === $valeur ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="field-error" data-field-error="objectif_eco" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

        <label>Rôle</label>
        <select name="role">
            <?php $r = (string) ($_POST['role'] ?? 'utilisateur'); ?>
            <option value="utilisateur" <?php echo $r === 'utilisateur' ? 'selected' : ''; ?>>utilisateur</option>
            <option value="admin" <?php echo $r === 'admin' ? 'selected' : ''; ?>>admin</option>
        </select>

        <label style="display:flex;align-items:center;gap:10px;margin-top:12px;">
            <input type="checkbox" name="est_actif" value="1" <?php echo isset($_POST['est_actif']) || !isset($_POST['nom_prenom']) ? 'checked' : ''; ?>>
            Actif
        </label>

        <div class="crud-actions" style="margin-top:18px;">
            <button class="crud-btn primary" type="submit">Enregistrer</button>
            <a class="crud-btn" href="<?php echo htmlspecialchars($urlListe, ENT_QUOTES, 'UTF-8'); ?>">Annuler</a>
        </div>
    </form>
</div>

<script>
// Validation JavaScript pour les formulaires utilisateur

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.crud-form');

    forms.forEach(form => {
        const errorDiv = form.parentNode.querySelector('div[style*="background:#f8d7da"]') || createErrorDiv(form);

        function createErrorDiv(f) {
            const div = document.createElement('div');
            div.style.cssText = 'background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;padding:10px 12px;border-radius:8px;margin:10px 0;display:none;';
            f.parentNode.insertBefore(div, f);
            return div;
        }

        form.addEventListener('submit', function(e) {
            clearFieldErrors(form);
            const fieldMessages = {};
            const errors = [];
            const isAjout = form.id === 'form-ajout';

            const nom = form.querySelector('input[name="nom_prenom"]').value.trim();
            const email = form.querySelector('input[name="email"]').value.trim();
            const mdp = form.querySelector('input[name="mot_de_passe"]').value;
            const role = form.querySelector('select[name="role"]').value;
            const niv = form.querySelector('select[name="niveau_activite"]').value;
                addFieldError('niveau_activite', 'Niveau d’activité invalide.');
            }
            const regimesAutorises = ['', 'omnivore', 'vegetarien', 'vegan', 'pescetarien', 'sans_gluten', 'sans_lactose'];
            if (!regimesAutorises.includes(regime)) {
                addFieldError('regime_alimentaire', 'Régime alimentaire invalide.');
            }
            const objectifsEcoAutorises = ['', 'reduire_dechets', 'reduire_plastique', 'manger_local', 'reduire_carbone', 'anti_gaspillage'];
            if (!objectifsEcoAutorises.includes(objE)) {
                addFieldError('objectif_eco', 'Objectif éco invalide.');
            }
            if (objS.length > 120) {
                addFieldError('objectif_sante', 'Objectif santé trop long (max 120 caractères).');
            }

            Object.entries(fieldMessages).forEach(([field, message]) => setFieldError(form, field, message));

            if (errors.length > 0) {
                e.preventDefault();
                errorDiv.textContent = 'Veuillez corriger les erreurs ci-dessous.';
                errorDiv.style.display = 'block';
                return false;
            }

            errorDiv.style.display = 'none';
        });
    });

    function clearFieldErrors(form) {
        form.querySelectorAll('.field-error').forEach(span => { span.textContent = ''; });
        form.querySelectorAll('input, select').forEach(field => { field.style.borderColor = ''; });
    }

    function setFieldError(form, name, message) {
        const span = form.querySelector('[data-field-error="' + name + '"]');
        const field = form.querySelector('[name="' + name + '"]');
        if (span) {
            span.textContent = message;
        }
        if (field) {
            field.style.borderColor = '#c00';
        }
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>

