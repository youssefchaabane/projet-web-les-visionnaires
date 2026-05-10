<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';

$base = app_base_from_script();
$urlAccueil = $base . '/view/accueil.php';
$error = '';

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ' . $urlAccueil);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../controller/utilisateurC.php';
    $ctrl = new UtilisateurC();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['mot_de_passe'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Veuillez saisir l\'email et le mot de passe.';
    } else {
        $user = $ctrl->findByEmailForAuth($email);
        if ($user === null || !password_verify($password, $user['mot_de_passe'])) {
            $error = 'Email ou mot de passe incorrect.';
        } elseif ($user['role'] !== 'admin') {
            $error = 'Accès réservé aux administrateurs.';
        } elseif ($user['est_actif'] != 1) {
            $error = 'Ce compte n\'est pas actif.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nom_prenom'] = $user['nom_prenom'];
            header('Location: ' . $urlAccueil);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Backoffice — ECOSAVE</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('../assets/css/background_connexion admin.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
            animation: zoom3DBackground 12s ease-in-out infinite;
        }

        @keyframes zoom3DBackground {
            0% {
                background-size: 100%;
                background-position: center;
            }
            25% {
                background-size: 110%;
                background-position: center 10%;
            }
            50% {
                background-size: 120%;
                background-position: center 20%;
            }
            75% {
                background-size: 110%;
                background-position: center 10%;
            }
            100% {
                background-size: 100%;
                background-position: center;
            }
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
            animation: parallax3D 15s ease-in-out infinite;
        }

        @keyframes parallax3D {
            0% {
                transform: perspective(1000px) rotateX(0deg) translateZ(0px);
                opacity: 0.3;
            }
            25% {
                transform: perspective(1000px) rotateX(2deg) translateZ(20px);
                opacity: 0.4;
            }
            50% {
                transform: perspective(1000px) rotateX(5deg) translateZ(50px);
                opacity: 0.5;
            }
            75% {
                transform: perspective(1000px) rotateX(2deg) translateZ(20px);
                opacity: 0.4;
            }
            100% {
                transform: perspective(1000px) rotateX(0deg) translateZ(0px);
                opacity: 0.3;
            }
        }

        body::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: zoom3D 8s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes zoom3D {
            0% {
                transform: scale(1) translate(0, 0);
                opacity: 0.3;
            }
            25% {
                transform: scale(1.2) translate(-10px, -10px);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.5) translate(-20px, -20px);
                opacity: 0.7;
            }
            75% {
                transform: scale(1.2) translate(-10px, -10px);
                opacity: 0.5;
            }
            100% {
                transform: scale(1) translate(0, 0);
                opacity: 0.3;
            }
        }
        .auth-card {
            width: min(420px, 92vw);
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, .20);
            padding: 26px;
            overflow: hidden;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            animation: floatCard 6s ease-in-out infinite, zoomCard 8s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes zoomCard {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }
        h1 { margin: 0 0 6px; color: #1b5e20; font-size: 24px; }
        p { margin: 0 0 18px; color: #666; }
        label { display: block; margin: 12px 0 6px; font-weight: 600; }
        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cfe0d5;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            width: 100%;
            margin-top: 16px;
            padding: 11px 12px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #27ae60, #16a085);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
            border-radius: 8px;
            padding: 9px 10px;
            margin-bottom: 10px;
        }
        .link-row {
            margin-top: 18px;
            text-align: center;
        }
        .link-row a {
            color: #1b5e20;
            text-decoration: none;
            font-weight: 600;
        }
        .logo-header {
            font-size: 18px;
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 16px;
            text-align: center;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="logo-header">🌱 ECOSAVE</div>
    <h1>Admin Backoffice</h1>
    <p>Connectez-vous avec l'email et le mot de passe administrateur pour accéder au tableau de bord.</p>
    <?php if ($error !== ''): ?>
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" id="adminLoginForm">
        <label for="email">Email</label>
        <input id="email" type="text" name="email">
        <span class="field-error" data-field-error="email" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
        <label for="mot_de_passe">Mot de passe</label>
        <input id="mot_de_passe" type="text" name="mot_de_passe">
        <span class="field-error" data-field-error="mot_de_passe" style="display:block;color:#c00;font-size:0.9rem;margin:4px 0 12px;"></span>
        <button type="submit">Se connecter</button>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adminLoginForm');
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
    <div class="link-row">
        <a href="Main.php">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>
