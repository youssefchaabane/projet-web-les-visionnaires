<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOSAVE</title>
    <link rel="stylesheet" href="../assets/css/client.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: url('../assets/css/Group of vegetables and fruits on.jpg') center center / cover fixed no-repeat;
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            padding-top: 90px;
            animation: fadeIn 0.4s ease-in;
            min-height: 100vh;
            position: relative;
        }
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-container {
            background: rgba(0, 0, 0, 0.25);
            padding: 16px 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,.18);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 200;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
            color: #4caf50;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 60px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-title {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            color: #ffffff;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.6);
        }

        .hero-subtitle {
            font-size: 20px;
            font-weight: 400;
            margin-bottom: 40px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .cta-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-button {
            display: inline-block;
            padding: 16px 32px;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-primary {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .cta-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 35px rgba(76, 175, 80, 0.4);
        }

        .cta-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .cta-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        .features-section {
            margin-bottom: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }

        .feature-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .feature-description {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .team-section {
            margin-bottom: 60px;
            padding: 0 20px;
        }

        .team-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .team-logo {
            margin-bottom: 30px;
        }

        .team-logo-img {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .team-card:hover .team-logo-img {
            transform: scale(1.05);
        }

        .team-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .team-description {
            font-size: 18px;
            line-height: 1.8;
            color: #ffffff;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .team-photos-section {
            margin-bottom: 60px;
            padding: 0 20px;
        }

        .team-photos-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-members-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 80px;
            justify-items: center;
            max-width: 900px;
            margin: 0 auto;
        }

        .team-member {
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
        }

        .team-member:hover {
            transform: translateY(-10px) scale(1.05);
        }

        .team-member .name-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 10;
            white-space: nowrap;
            font-weight: 700;
            font-size: 18px;
            color: #333;
        }

        .team-member:hover .name-overlay {
            opacity: 1;
        }

        .member-photo {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .team-member:hover .member-photo {
            background: white;
            border-color: white;
            transform: scale(1.1);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            
            .nav-links {
                display: none;
            }
            
            .main-content {
                padding: 20px 15px;
            }
            
            .cta-container {
                flex-direction: column;
                align-items: center;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }

            .team-card {
                padding: 30px 20px;
            }

            .team-title {
                font-size: 28px;
            }

            .team-description {
                font-size: 16px;
            }

            .team-logo-img {
                max-width: 120px;
            }

            .team-members-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 60px;
                max-width: 500px;
                margin: 0 auto;
            }

            .member-photo {
                width: 120px;
                height: 120px;
                cursor: pointer;
            }

        .team-member .name-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 10;
            white-space: nowrap;
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }

        .team-member:hover .name-overlay {
            opacity: 1;
        }
        }

        /* Modal Styles */
        .member-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f0f0f0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .modal-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            .modal-content {
                padding: 30px 20px;
                max-width: 300px;
            }

            .modal-photo {
                width: 150px;
                height: 150px;
            }

            .modal-name {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header-container">
        <a href="accueil.php" class="logo">
            <div class="logo-icon">🌱</div>
            <span>ECOSAVE</span>
        </a>
        <nav class="nav-links">
            <a href="Main.php" class="nav-link">Accueil</a>
            <a href="login.php" class="nav-link">Connexion</a>
            <a href="admin_login.php" class="nav-link">Admin</a>
            <a href="#features" class="nav-link">Fonctionnalité</a>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero-section">
            <h1 class="hero-title">Bienvenue sur ECOSAVE</h1>
            <p class="hero-subtitle">
                Votre allié pour un mode de vie durable. Découvrez une plateforme intuitive conçue pour vous aider à réduire votre empreinte carbone, optimiser votre santé et préserver notre planète, un geste à la fois.
            </p>
                    </section>

        <section class="team-section">
            <div class="team-card">
                <div class="team-logo">
                    <img src="../assets/css/logo_fac.png" alt="Logo FAC" class="team-logo-img">
                </div>
                <div class="team-content">
                    <h2 class="team-title">Notre Équipe Visionnaire</h2>
                    <p class="team-description">
                        Nous sommes une équipe de 6 passionnés de l'ESPRIT, unis par la technologie et l'amour de la nature. Notre mission ? Développer cette plateforme pour transformer notre empreinte carbone en un levier de changement mondial. Ensemble, nous innovons pour protéger notre planète et bâtir un avenir plus durable. 🌱✨
                    </p>
                </div>
            </div>
        </section>

        <!-- Team Photos Section -->
        <section class="team-photos-section">
            <div class="team-photos-container">
                <div class="team-members-grid">
                    <div class="team-member" data-name="Ahmed Hajji">
                        <img src="../assets/css/ahmed.png" alt="Ahmed" class="member-photo">
                        <div class="name-overlay">Ahmed Hajji</div>
                    </div>
                    <div class="team-member" data-name="Dhia Eddine Hamdi">
                        <img src="../assets/css/dhia.png" alt="Dhia" class="member-photo">
                        <div class="name-overlay">Dhia Eddine Hamdi</div>
                    </div>
                    <div class="team-member" data-name="Ghofrane Barhoumi">
                        <img src="../assets/css/ghofrane.png" alt="Ghofrane" class="member-photo">
                        <div class="name-overlay">Ghofrane Barhoumi</div>
                    </div>
                    <div class="team-member" data-name="Hiba Riahi">
                        <img src="../assets/css/hiba_r.png" alt="Hiba R" class="member-photo">
                        <div class="name-overlay">Hiba Riahi</div>
                    </div>
                    <div class="team-member" data-name="Hiba Lamloum">
                        <img src="../assets/css/hiba_l.png" alt="Hiba L" class="member-photo">
                        <div class="name-overlay">Hiba Lamloum</div>
                    </div>
                    <div class="team-member" data-name="Youssef Chaabeen">
                        <img src="../assets/css/youssef.png" alt="Youssef" class="member-photo">
                        <div class="name-overlay">Youssef Chaabeen</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Member Name Modal -->
        <div id="memberModal" class="member-modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <div class="member-info">
                    <img id="modalPhoto" src="" alt="" class="modal-photo">
                    <h2 id="modalName" class="modal-name"></h2>
                </div>
            </div>
        </div>

        <section id="features" class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon">🌍</span>
                    <h3 class="feature-title">Ton empreinte éco</h3>
                    <p class="feature-description">
                        Suivez ton impact environnemental au quotidien et découvre comment réduire tes émissions pour protéger la planète 🌍
                    </p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">❤️</span>
                    <h3 class="feature-title">Ton espace santé</h3>
                    <p class="feature-description">
                        Configure tes allergies et préférences nous veillons à ce que chaque suggestion soit sur et adapté à tes besoins ❤️
                    </p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📝</span>
                    <h3 class="feature-title">Publication</h3>
                    <p class="feature-description">
                        Créez et partagez du contenu sur l'écologie, l'alimentation durable et les actions de votre communauté.
                    </p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📖</span>
                    <h3 class="feature-title">Ton carnet de recettes</h3>
                    <p class="feature-description">
                        Ton carnet de recettes accéde à des idées de recettes créatives et durables spécialement sélectionnées selon tes gouts et ce qu'il reste dans ta cuisine.
                    </p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🏪</span>
                    <h3 class="feature-title">Ta réserve durable</h3>
                    <p class="feature-description">
                        Organisez votre cuisine avec amour et conscience suivez vos produits essentiels et assurez-vous de toujours avoir ce qu'il vous faut pour vos recettes préférées.
                    </p>
                </div>
            </div>
        </section>
    </main>
<script>
        // Modal functionality
        function showModal(name, photoSrc) {
            const modal = document.getElementById('memberModal');
            const modalName = document.getElementById('modalName');
            const modalPhoto = document.getElementById('modalPhoto');
            
            modalName.textContent = name;
            modalPhoto.src = photoSrc;
            modalPhoto.alt = name;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            const modal = document.getElementById('memberModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Add click event listeners to team members
        document.addEventListener('DOMContentLoaded', function() {
            const teamMembers = document.querySelectorAll('.team-member');
            
            teamMembers.forEach(member => {
                member.addEventListener('click', function() {
                    const name = this.getAttribute('data-name');
                    const photo = this.querySelector('.member-photo').src;
                    showModal(name, photo);
                });
            });
            
            // Close modal when clicking outside
            document.getElementById('memberModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>
