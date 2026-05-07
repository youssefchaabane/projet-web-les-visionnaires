<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';

$base = app_base_from_script();
$urlAccueil = $base . '/view/accueil.php';
$allowedRedirects = ['accueil.php', 'user_home.php'];
$redirect = '';
$activeTab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect = trim((string) ($_POST['redirect'] ?? ''));
    $activeTab = ($_POST['form_action'] ?? '') === 'register' ? 'register' : 'login';
} else {
    $redirect = trim((string) ($_GET['redirect'] ?? ''));
    if (trim((string) ($_GET['tab'] ?? '')) === 'register') {
        $activeTab = 'register';
    }
}
if (!in_array($redirect, $allowedRedirects, true)) {
    $redirect = '';
}

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . $urlAccueil);
    } else {
        header('Location: ' . $base . '/view/user_home.php');
    }
    exit;
}

$erreur = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../controller/utilisateurC.php';
    $ctrl = new UtilisateurC();

    $form_action = $_POST['form_action'] ?? '';

    if ($form_action === 'login') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['mot_de_passe'] ?? '');

        if ($email === '' || $password === '') {
            $erreur = 'Veuillez saisir email et mot de passe.';
        } else {
            $user = $ctrl->findByEmailForAuth($email);
            if ($user === null || !password_verify($password, $user['mot_de_passe'])) {
                $erreur = 'Email ou mot de passe incorrect.';
            } elseif ($user['est_actif'] != 1) {
                $erreur = 'Ce compte n\'est pas actif.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nom_prenom'] = $user['nom_prenom'];

                if ($redirect === 'user_home.php') {
                    $redirectUrl = $base . '/view/user_home.php';
                } elseif ($redirect === 'accueil.php' && $user['role'] === 'admin') {
                    $redirectUrl = $urlAccueil;
                } else {
                    $redirectUrl = $user['role'] === 'admin' ? $urlAccueil : $base . '/view/user_home.php';
                }

                header('Location: ' . $redirectUrl);
                exit;
            }
        }
    } elseif ($form_action === 'register') {
        $nom_prenom = trim((string) ($_POST['nom_prenom'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['mot_de_passe'] ?? '');
        $niveau_activite = trim((string) ($_POST['niveau_activite'] ?? ''));
        $regime_alimentaire = trim((string) ($_POST['regime_alimentaire'] ?? ''));
        $objectif_sante = trim((string) ($_POST['objectif_sante'] ?? ''));
        $objectif_eco = trim((string) ($_POST['objectif_eco'] ?? ''));

        if ($nom_prenom === '' || $email === '' || $password === '' || strlen($password) < 8) {
            $erreur = 'Veuillez remplir tous les champs. Mot de passe minimum 8 caractères.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = 'Adresse e-mail invalide.';
        } elseif (!in_array($niveau_activite, ['', 'sédentaire', 'léger', 'modéré', 'intense'], true)) {
            $erreur = 'Niveau d\'activité invalide.';
        } elseif (!in_array($regime_alimentaire, ['', 'omnivore', 'vegetarien', 'vegan', 'pescetarien', 'sans_gluten', 'sans_lactose'], true)) {
            $erreur = 'Régime alimentaire invalide.';
        } elseif (!in_array($objectif_eco, ['', 'reduire_dechets', 'reduire_plastique', 'manger_local', 'reduire_carbone', 'anti_gaspillage'], true)) {
            $erreur = 'Objectif éco invalide.';
        } else {
            try {
                $ctrl->register([
                    'nom_prenom' => $nom_prenom,
                    'email' => $email,
                    'mot_de_passe' => $password,
                    'niveau_activite' => $niveau_activite,
                    'regime_alimentaire' => $regime_alimentaire,
                    'objectif_sante' => $objectif_sante,
                    'objectif_eco' => $objectif_eco,
                    'role' => 'utilisateur',
                ]);
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            } catch (PDOException $e) {
                $code = (int) ($e->errorInfo[1] ?? 0);
                if ($code === 1062) {
                    $erreur = 'Cet email est déjà utilisé.';
                } else {
                    $erreur = 'Erreur lors de l\'inscription.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion / Inscription — ECOSAVE</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027, #1a1f3a);
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Elements */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 25%, rgba(39, 174, 96, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(26, 188, 156, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 10%, rgba(241, 196, 15, 0.08) 0%, transparent 50%);
            pointer-events: none;
            animation: ecoPulse 15s ease-in-out infinite;
        }
        
        body::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(39, 174, 96, 0.03) 10px,
                    rgba(39, 174, 96, 0.03) 20px
                );
            animation: ecoRotate 60s linear infinite;
            pointer-events: none;
        }
        
        /* Floating Elements */
        .eco-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(39, 174, 96, 0.6);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .eco-particle:nth-child(1) { top: 20%; left: 10%; animation: float1 12s ease-in-out infinite; }
        .eco-particle:nth-child(2) { top: 60%; left: 80%; animation: float2 15s ease-in-out infinite; }
        .eco-particle:nth-child(3) { top: 30%; left: 70%; animation: float3 18s ease-in-out infinite; }
        .eco-particle:nth-child(4) { top: 80%; left: 20%; animation: float4 10s ease-in-out infinite; }
        .eco-particle:nth-child(5) { top: 10%; left: 60%; animation: float5 20s ease-in-out infinite; }
        
        @keyframes ecoPulse {
            0%, 100% { opacity: 0.8; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }
        
        @keyframes ecoRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
            25% { transform: translate(30px, -20px) scale(1.2); opacity: 0.8; }
            50% { transform: translate(-20px, 30px) scale(0.8); opacity: 0.4; }
            75% { transform: translate(20px, 20px) scale(1.1); opacity: 0.7; }
        }
        
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            33% { transform: translate(-25px, 15px) scale(1.3); opacity: 0.9; }
            66% { transform: translate(15px, -25px) scale(0.7); opacity: 0.6; }
        }
        
        @keyframes float3 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); opacity: 0.7; }
            50% { transform: translate(40px, 20px) rotate(180deg); opacity: 0.3; }
        }
        
        @keyframes float4 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            25% { transform: translate(-30px, -30px) scale(1.4); opacity: 0.8; }
            75% { transform: translate(30px, 30px) scale(0.6); opacity: 0.5; }
        }
        
        @keyframes float5 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.6; }
            40% { transform: translate(20px, -40px) scale(0.8); opacity: 0.9; }
            80% { transform: translate(-40px, 20px) scale(1.2); opacity: 0.4; }
        }
        .auth-card {
            width: min(480px, 92vw);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
            padding: 26px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.6s ease-out;
            position: relative;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.05) 50%, 
                transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
            50% { transform: translateX(-30%) translateY(-50%) rotate(1deg); }
        }
        .tabs {
            display: flex;
        }
        .tab {
            flex: 1;
            padding: 12px;
            border: none;
            background: rgba(248, 249, 250, 0.8);
            font-size: 15px;
            font-weight: 700;
            color: #888;
            cursor: pointer;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .tab:hover::before {
            left: 100%;
        }
        .tab.active {
            background: white;
            color: #27ae60;
            border-bottom: 3px solid #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.15);
        }
        .home-container {
            display: flex;
            margin-top: 8px;
        }
        .home-btn {
            flex: 1;
            padding: 12px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: all .25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .home-btn:hover {
            background: #229954;
            transform: translateY(-1px);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 9px 10px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .tab:hover:not(.active) { background: #f0f0f0; color: #555; }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
        h1 { margin: 0 0 6px; color: #1b5e20; font-size: 24px; }
        p { margin: 0 0 18px; color: #666; }
        label { display: block; margin: 12px 0 6px; font-weight: 600; }
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(207, 224, 213, 0.6);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #27ae60;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
            background: white;
            transform: translateY(-1px);
        }
        
        input::placeholder {
            color: #999;
        }
        button {
            width: 100%;
            margin-top: 16px;
            padding: 12px 20px;
            border: 0;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #27ae60, #16a085);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            transform: translateY(0);
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
            border-radius: 8px;
            padding: 9px 10px;
            margin-bottom: 10px;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 9px 10px;
            margin-bottom: 10px;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        @media(max-width: 500px){ .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<!-- Floating Background Elements -->
<div class="eco-particle"></div>
<div class="eco-particle"></div>
<div class="eco-particle"></div>
<div class="eco-particle"></div>
<div class="eco-particle"></div>

<div class="auth-card">
    <div class="tabs">
        <button class="tab <?php echo $activeTab === 'login' ? 'active' : ''; ?>" id="tab-login" onclick="switchTab('login')">Connexion</button>
        <button class="tab <?php echo $activeTab === 'register' ? 'active' : ''; ?>" id="tab-register" onclick="switchTab('register')">Inscription</button>
    </div>
    <div class="form-panel <?php echo $activeTab === 'login' ? 'active' : ''; ?>" id="panel-login">
        <h1>Connexion</h1>
        <p>Entrez vos identifiants.</p>
        <?php if ($erreur !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="form_action" value="login">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="email">Email</label>
            <input id="email" type="text" name="email">
            <span class="field-error" data-field-error="email" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
                        <label for="mot_de_passe">Mot de passe</label>
            <input id="mot_de_passe" type="text" name="mot_de_passe">
            <span class="field-error" data-field-error="mot_de_passe" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
            <button type="submit">Se connecter</button>
        </form>
        <div class="home-container">
            <a href="<?php echo app_base_from_script(); ?>" class="home-btn">🏠 Accueil</a>
        </div>
    </div>
    <div class="form-panel <?php echo $activeTab === 'register' ? 'active' : ''; ?>" id="panel-register">
        <h1>Inscription</h1>
        <p>Créez votre compte.</p>
        <?php if ($erreur !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="form_action" value="register">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-row">
                <div>
                    <label for="nom_prenom">Nom et Prénom</label>
                    <input id="nom_prenom" type="text" name="nom_prenom" maxlength="150">
                    <span class="field-error" data-field-error="nom_prenom" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
                </div>
                <div>
                    <label for="email_reg">Email</label>
                    <input id="email_reg" type="text" name="email" maxlength="190">
                    <span class="field-error" data-field-error="email" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
                </div>
            </div>
            <label for="mot_de_passe_reg">Mot de passe</label>
            <input id="mot_de_passe_reg" type="text" name="mot_de_passe">
            <span class="field-error" data-field-error="mot_de_passe" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

            <label for="niveau_activite">Niveau d'activité</label>
            <select id="niveau_activite" name="niveau_activite">
                <option value="">—</option>
                <option value="sédentaire">Sédentaire</option>
                <option value="léger">Léger</option>
                <option value="modéré">Modéré</option>
                <option value="intense">Intense</option>
            </select>
            <span class="field-error" data-field-error="niveau_activite" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>

            <label for="regime_alimentaire">Régime alimentaire</label>
            <select id="regime_alimentaire" name="regime_alimentaire">
                <option value="">—</option>
                <option value="omnivore">Omnivore</option>
                <option value="vegetarien">Végétarien</option>
                <option value="vegan">Vegan</option>
                <option value="pescetarien">Pescétarien</option>
                <option value="sans_gluten">Sans gluten</option>
                <option value="sans_lactose">Sans lactose</option>
            </select>

            <label for="objectif_sante">Objectif santé</label>
            <input id="objectif_sante" type="text" name="objectif_sante" maxlength="120">

            <label for="objectif_eco">Objectif éco</label>
            <select id="objectif_eco" name="objectif_eco">
                <option value="">—</option>
                <option value="reduire_dechets">Réduire les déchets</option>
                <option value="reduire_plastique">Réduire le plastique</option>
                <option value="manger_local">Manger local</option>
                <option value="reduire_carbone">Réduire l'empreinte carbone</option>
                <option value="anti_gaspillage">Lutter contre le gaspillage</option>
            </select>

            <input type="hidden" name="role" value="utilisateur">
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</div>
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const errorDiv = form.parentNode.querySelector('.error') || createErrorDiv(form);

        function createErrorDiv(f) {
            const div = document.createElement('div');
            div.className = 'error';
            div.style.display = 'none';
            f.parentNode.insertBefore(div, f);
            return div;
        }

        form.addEventListener('submit', function(e) {
            clearFieldErrors(form);
            const fieldMessages = {};
            const errors = [];

            const emailInput = form.querySelector('input[name="email"]');
            const passwordInput = form.querySelector('input[name="mot_de_passe"]');
            const nameInput = form.querySelector('input[name="nom_prenom"]');

            // Validation email
            if (emailInput) {
                const email = emailInput.value.trim();
                if (email === '') {
                    fieldMessages['email'] = 'L\'email est obligatoire.';
                    errors.push('email');
                } else {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        fieldMessages['email'] = 'Adresse e-mail invalide.';
                        errors.push('email');
                    } else if (email.length > 190) {
                        fieldMessages['email'] = "L'e-mail est trop long (max 190 caractères).";
                        errors.push('email');
                    }
                }
            }

            // Validation mot de passe
            if (passwordInput) {
                const password = passwordInput.value;
                if (password === '') {
                    fieldMessages['mot_de_passe'] = 'Le mot de passe est obligatoire.';
                    errors.push('mot_de_passe');
                } else if (password.length < 6) {
                    fieldMessages['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
                    errors.push('mot_de_passe');
                }
            }

            // Validation nom (pour inscription)
            if (nameInput) {
                const nom = nameInput.value.trim();
                if (nom === '') {
                    fieldMessages['nom_prenom'] = 'Le nom est obligatoire.';
                    errors.push('nom_prenom');
                } else if (nom.length < 2 || nom.length > 150) {
                    fieldMessages['nom_prenom'] = 'Le nom doit contenir entre 2 et 150 caractères.';
                    errors.push('nom_prenom');
                }
            }

            // Afficher les erreurs de champ
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
});
</script>
</body>
</html>
