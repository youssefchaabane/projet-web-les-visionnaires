<?php

declare(strict_types=1);



require_once __DIR__ . '/partials/auth.php';

require_login();

require_once __DIR__ . '/../controller/utilisateurC.php';



$userId = (int) ($_SESSION['user_id'] ?? 0);

$controller = new UtilisateurC();

$user = $controller->recuperer($userId);

if ($user === null) {

    header('Location: ' . app_base_from_script() . '/view/logout.php');

    exit;

}



$pageTitle = 'Mon espace ECOSAVE';

require __DIR__ . '/partials/header.php';

?>



<style>

.dashboard-container {

    max-width: 1400px;

    margin: 0 auto;

    padding: 30px;

    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    background: linear-gradient(135deg, #f8faf8, #f1f5f9);

    min-height: 100vh;

}



.dashboard-header {

    background: linear-gradient(135deg, #10b981, #059669);

    color: white;

    padding: 40px;

    border-radius: 20px;

    box-shadow: 0 25px 50px rgba(16, 185, 129, 0.15);

    margin-bottom: 40px;

    position: relative;

    overflow: hidden;

    backdrop-filter: blur(10px);

    border: 1px solid rgba(255, 255, 255, 0.1);

}



.dashboard-header::before {

    content: '';

    position: absolute;

    top: -50%;

    right: -10%;

    width: 400px;

    height: 400px;

    background: radial-gradient(circle, rgba(255,255,255,0.15) 2px, transparent 2px);

    background-size: 20px 20px;

    animation: float 20s linear infinite;

}



@keyframes float {

    0% { transform: rotate(0deg); }

    100% { transform: rotate(360deg); }

}



.user-profile {

    display: flex;

    align-items: center;

    gap: 25px;

    margin-bottom: 30px;

    position: relative;

    z-index: 2;

}



.profile-photo {

    position: relative;

    width: 120px;

    height: 120px;

}



.profile-photo img {

    width: 100%;

    height: 100%;

    border-radius: 50%;

    object-fit: cover;

    border: 4px solid rgba(255, 255, 255, 0.2);

    transition: transform 0.3s ease;

}



.profile-photo:hover img {

    transform: scale(1.05);

}



.photo-overlay {

    position: absolute;

    top: 0;

    left: 0;

    right: 0;

    bottom: 0;

    background: rgba(0, 0, 0, 0.7);

    border-radius: 50%;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    opacity: 0;

    transition: opacity 0.3s ease;

    cursor: pointer;

}



.profile-photo:hover .photo-overlay {

    opacity: 1;

}



.photo-overlay span {

    color: white;

    font-size: 24px;

    margin-bottom: 5px;

}



.photo-overlay span:last-child {

    font-size: 12px;

}



.user-welcome {

    flex: 1;

}



.user-welcome h1 {

    font-size: 32px;

    font-weight: 800;

    margin-bottom: 8px;

    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);

}



.user-welcome p {

    font-size: 18px;

    opacity: 0.9;

    margin: 0;

}



.user-badge {

    background: rgba(255, 255, 255, 0.2);

    padding: 8px 16px;

    border-radius: 20px;

    font-weight: 600;

    font-size: 14px;

    text-transform: uppercase;

    letter-spacing: 1px;

    backdrop-filter: blur(10px);

}



.dashboard-stats {

    display: grid;

    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

    gap: 20px;

    margin-top: 30px;

    position: relative;

    z-index: 2;

}



.stat-card {

    background: rgba(255, 255, 255, 0.1);

    backdrop-filter: blur(10px);

    padding: 20px;

    border-radius: 15px;

    text-align: center;

    border: 1px solid rgba(255, 255, 255, 0.2);

    transition: transform 0.3s ease;

}



.stat-card:hover {

    transform: translateY(-5px);

}



.stat-value {

    font-size: 28px;

    font-weight: 800;

    margin-bottom: 8px;

}



.stat-label {

    font-size: 14px;

    opacity: 0.8;

    text-transform: uppercase;

    letter-spacing: 1px;

}



.navigation-icons {

    display: flex;

    justify-content: center;

    gap: 30px;

    margin: 40px 0;

    flex-wrap: wrap;

}



.nav-icon {

    display: flex;

    flex-direction: column;

    align-items: center;

    text-decoration: none;

    color: #374151;

    transition: all 0.3s ease;

    padding: 20px;

    border-radius: 15px;

    background: white;

    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);

    min-width: 120px;

}



.nav-icon:hover {

    transform: translateY(-5px);

    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);

    color: #10b981;

}



.nav-icon-icon {

    width: 60px;

    height: 60px;

    background: linear-gradient(135deg, #10b981, #059669);

    border-radius: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 28px;

    color: white;

    margin-bottom: 10px;

    transition: all 0.3s ease;

}



.nav-icon:hover .nav-icon-icon {

    transform: scale(1.1);

    background: linear-gradient(135deg, #059669, #047857);

}



.nav-icon-label {

    font-size: 14px;

    font-weight: 600;

    text-align: center;

}



.dashboard-content {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 30px;

    margin-top: 40px;

}



.content-card {

    background: white;

    border-radius: 20px;

    padding: 30px;

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);

    transition: transform 0.3s ease;

    width: 100%;

    max-width: 600px;

}



.content-card {

    background: white;

    border-radius: 20px;

    padding: 30px;

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);

    transition: transform 0.3s ease;

}



.content-card:hover {

    transform: translateY(-5px);

}



.card-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 25px;

    padding-bottom: 15px;

    border-bottom: 2px solid #f1f5f9;

}



.card-title {

    font-size: 22px;

    font-weight: 700;

    color: #1f2937;

    display: flex;

    align-items: center;

    gap: 10px;

}



.card-icon {

    width: 40px;

    height: 40px;

    background: linear-gradient(135deg, #10b981, #059669);

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    font-size: 20px;

}



.card-action {

    color: #10b981;

    text-decoration: none;

    font-weight: 600;

    font-size: 14px;

    transition: color 0.3s ease;

}



.card-action:hover {

    color: #059669;

}



.progress-item {

    margin-bottom: 20px;

}



.info-item {

    display: flex;

    align-items: center;

    gap: 15px;

    padding: 15px;

    background: #f8fafc;

    border-radius: 12px;

    margin-bottom: 15px;

    transition: all 0.3s ease;

}



.info-item:hover {

    background: #f1f5f9;

    transform: translateY(-2px);

}



.info-icon {

    width: 40px;

    height: 40px;

    background: linear-gradient(135deg, #10b981, #059669);

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    font-size: 20px;

    flex-shrink: 0;

}



.info-content {

    flex: 1;

    display: flex;

    justify-content: space-between;

    align-items: center;

}



.info-label {

    font-size: 14px;

    font-weight: 600;

    color: #64748b;

}



.info-value {

    font-size: 16px;

    font-weight: 700;

    color: #1f2937;

}



.progress-label {

    display: flex;

    justify-content: space-between;

    margin-bottom: 8px;

    font-weight: 600;

    color: #374151;

}



.progress-bar {

    height: 8px;

    background: #f3f4f6;

    border-radius: 4px;

    overflow: hidden;

}



.progress-fill {

    height: 100%;

    background: linear-gradient(90deg, #10b981, #059669);

    border-radius: 4px;

    transition: width 0.6s ease;

}



.goal-item {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 15px;

    background: #f8fafc;

    border-radius: 10px;

    margin-bottom: 15px;

    transition: background 0.3s ease;

}



.goal-item:hover {

    background: #f1f5f9;

}



.goal-info {

    flex: 1;

}



.goal-title {

    font-weight: 600;

    color: #1f2937;

    margin-bottom: 4px;

}



.goal-date {

    font-size: 14px;

    color: #6b7280;

}



.goal-status {

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;

    text-transform: uppercase;

}



.goal-status.active {

    background: #dcfce7;

    color: #16a34a;

}



.goal-status.completed {

    background: #dbeafe;

    color: #2563eb;

}



.recommendation-item {

    display: flex;

    align-items: start;

    gap: 15px;

    padding: 15px;

    background: #f8fafc;

    border-radius: 10px;

    margin-bottom: 15px;

    transition: background 0.3s ease;

}



.recommendation-item:hover {

    background: #f1f5f9;

}



.recommendation-icon {

    width: 40px;

    height: 40px;

    background: linear-gradient(135deg, #fbbf24, #f59e0b);

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    font-size: 20px;

    flex-shrink: 0;

}



.recommendation-content {

    flex: 1;

}



.recommendation-title {

    font-weight: 600;

    color: #1f2937;

    margin-bottom: 4px;

}



.recommendation-text {

    font-size: 14px;

    color: #6b7280;

    line-height: 1.5;

}



.team-section {

    margin-top: 40px;

    background: white;

    border-radius: 20px;

    padding: 30px;

    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);

}



.team-header {

    text-align: center;

    margin-bottom: 30px;

}



.team-title {

    font-size: 28px;

    font-weight: 700;

    color: #1f2937;

    margin-bottom: 10px;

}



.team-subtitle {

    font-size: 16px;

    color: #6b7280;

}



.team-grid {

    display: grid;

    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

    gap: 25px;

}



.team-member {

    text-align: center;

    transition: transform 0.3s ease;

}



.team-member:hover {

    transform: translateY(-5px);

}



.member-photo {

    width: 120px;

    height: 120px;

    border-radius: 50%;

    margin: 0 auto 15px;

    object-fit: cover;

    border: 4px solid #f3f4f6;

    transition: all 0.3s ease;

}



.team-member:hover .member-photo {

    border-color: #10b981;

    transform: scale(1.05);

}



.member-name {

    font-size: 18px;

    font-weight: 600;

    color: #1f2937;

    margin-bottom: 5px;

}



.member-role {

    font-size: 14px;

    color: #6b7280;

    margin-bottom: 10px;

}



.member-bio {

    font-size: 13px;

    color: #9ca3af;

    line-height: 1.4;

}



@media (max-width: 768px) {

    .dashboard-container {

        padding: 20px;

    }

    

    .dashboard-header {

        padding: 25px;

    }

    

    .user-profile {

        flex-direction: column;

        text-align: center;

        gap: 20px;

    }

    

    .user-welcome h1 {

        font-size: 28px;

    }

    

    .dashboard-content {

        grid-template-columns: 1fr;

        gap: 20px;

    }

    

    .content-card {

        padding: 20px;

    }

    

    .navigation-icons {

        gap: 15px;

    }

    

    .nav-icon {

        min-width: 100px;

        padding: 15px;

    }

    

    .nav-icon-icon {

        width: 50px;

        height: 50px;

        font-size: 24px;

    }

    

    .team-grid {

        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));

    }

}

</style>



<div class="dashboard-container">

    <div class="dashboard-header">

        <div class="user-profile">

            <div class="user-welcome">

                <h1>🌱 Bonjour <?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>!</h1>

                <p>Bienvenue sur votre compte</p>

            </div>

        </div>

        <div class="user-badge">

            <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?>

        </div>

        

            </div>



    <!-- Navigation Icons -->

    <div class="navigation-icons">

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">📝</div>

            <div class="nav-icon-label">Publication</div>

        </a>

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">🥗</div>

            <div class="nav-icon-label">Recettes</div>

        </a>

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">📦</div>

            <div class="nav-icon-label">Stock</div>

        </a>

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">🚫</div>

            <div class="nav-icon-label">Allergies</div>

        </a>

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">🌍</div>

            <div class="nav-icon-label">Empreinte</div>

        </a>

    </div>



    <div class="dashboard-content">

        <div class="content-card">

            <div class="card-header">

                <div class="card-title">

                    <div class="card-icon">👤</div>

                    Mes informations

                </div>

                <a href="profile.php" class="card-action">Modifier</a>

            </div>

            

            <div class="info-item">

                <div class="info-icon">📧</div>

                <div class="info-content">

                    <div class="info-label">Email</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">👤</div>

                <div class="info-content">

                    <div class="info-label">Nom complet</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['nom_prenom'], ENT_QUOTES, 'UTF-8'); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">🎂</div>

                <div class="info-content">

                    <div class="info-label">Date d'inscription</div>

                    <div class="info-value"><?php echo date('d/m/Y', strtotime($user['date_creation'] ?? 'now')); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">🏷️</div>

                <div class="info-content">

                    <div class="info-label">Rôle</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></div>

                </div>

            </div>

        </div>

        

            </div>



    </div>









<script>

document.addEventListener('DOMContentLoaded', function() {

    // Animation des barres de progression

    const progressFills = document.querySelectorAll('.progress-fill');

    progressFills.forEach(fill => {

        const width = fill.style.width;

        fill.style.width = '0';

        setTimeout(() => {

            fill.style.width = width;

        }, 100);

    });



    // Gestion du changement de photo de profil avec sauvegarde localStorage

    const profilePhoto = document.querySelector('.profile-photo');

    const profileImage = document.getElementById('profileImage');

    const photoOverlay = document.querySelector('.photo-overlay');

    

    // Charger la photo sauvegardée au chargement de la page

    window.addEventListener('DOMContentLoaded', function() {

        const savedPhoto = localStorage.getItem('userProfilePhoto');

        if (savedPhoto) {

            profileImage.src = savedPhoto;

        }

    });

    

    photoOverlay.addEventListener('click', function() {

        const input = document.createElement('input');

        input.type = 'file';

        input.accept = 'image/*';

        input.onchange = function(e) {

            const file = e.target.files[0];

            if (file) {

                const reader = new FileReader();

                reader.onload = function(e) {

                    const newPhotoSrc = e.target.result;

                    profileImage.src = newPhotoSrc;

                    // Sauvegarder la photo dans localStorage

                    localStorage.setItem('userProfilePhoto', newPhotoSrc);

                };

                reader.readAsDataURL(file);

    });

    

        const quickSuggestions = document.querySelectorAll('.quick-suggestion');

    quickSuggestions.forEach(button => {

        button.addEventListener('click', function() {

            const suggestion = this.getAttribute('data-suggestion');

            chatInput.value = suggestion;

            chatInput.focus();

            

            // Animation visuelle

            this.style.transform = 'scale(0.95)';

            setTimeout(() => {

                this.style.transform = '';

            }, 150);

        });

    });

});

</script>



<?php require __DIR__ . '/partials/footer.php'; ?>



<!-- Professional Chatbot - Modern Design -->

<div id="professional-chatbot" class="professional-chatbot">

    <div class="chatbot-toggle" id="chatbotToggle">

        <div class="chatbot-icon">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">

                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                <circle cx="9" cy="10" r="1" fill="currentColor"/>

                <circle cx="12" cy="10" r="1" fill="currentColor"/>

                <circle cx="15" cy="10" r="1" fill="currentColor"/>

            </svg>

        </div>

        <div class="chatbot-badge">

            <span>AI</span>

        </div>

        <div class="pulse-ring"></div>

    </div>

    

    <div class="chatbot-window" id="chatbotWindow">

        <div class="chatbot-header">

            <div class="chatbot-header-content">

                <div class="chatbot-avatar">

                    <div class="avatar-gradient"></div>

                    <svg width="32" height="32" viewBox="0 0 24 24" fill="white">

                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>

                    </svg>

                </div>

                <div class="chatbot-title-section">

                    <h3 class="chatbot-title">Assistant ECOSAVE Pro</h3>

                    <div class="chatbot-status">

                        <span class="status-dot"></span>

                        <span class="status-text">En ligne • Expert personnel</span>

                    </div>

                </div>

            </div>

            <button class="chatbot-close" id="chatbotClose">

                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">

                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>

                </svg>

            </button>

        </div>

        

        <div class="chatbot-messages" id="chatbotMessages">

            <div class="message bot-message">

                <div class="message-avatar">

                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">

                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>

                    </svg>

                </div>

                <div class="message-content">

                    <div class="message-header">

                        <span class="sender-name">Assistant ECOSAVE Pro</span>

                        <span class="message-time">Maintenant</span>

                    </div>

                    <div class="message-text">

                        👋 Bonjour <?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?> !<br><br>

                        Je suis votre assistant personnel ECOSAVE, spécialisé en nutrition, sport et bien-être. J'ai analysé votre profil :<br>

                        🥗 <strong>Régime:</strong> <?php echo htmlspecialchars($user['regime_alimentaire'] ?: 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?><br>

                        🎯 <strong>Objectif:</strong> <?php echo htmlspecialchars($user['objectif_sante'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?><br>

                        💪 <strong>Niveau d'activité:</strong> <?php echo htmlspecialchars($user['niveau_activite'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?><br><br>

                        Comment puis-je vous aider aujourd'hui ?

                    </div>

                </div>

            </div>

        </div>

        

        <div class="chatbot-input-area">

            <div class="quick-actions">

                <button class="quick-action-btn" data-action="recette">

                    <span class="btn-icon">🥗</span>

                    <span class="btn-text">Recettes</span>

                </button>

                <button class="quick-action-btn" data-action="sport">

                    <span class="btn-icon">💪</span>

                    <span class="btn-text">Sport</span>

                </button>

                <button class="quick-action-btn" data-action="conseil">

                    <span class="btn-icon">💡</span>

                    <span class="btn-text">Conseils</span>

                </button>

                <button class="quick-action-btn" data-action="objectif">

                    <span class="btn-icon">🎯</span>

                    <span class="btn-text">Objectifs</span>

                </button>

            </div>

            <div class="input-container">

                <input type="text" id="chatbotInput" placeholder="Posez-moi votre question..." class="chatbot-input">

                <button id="chatbotSend" class="send-button">

                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">

                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                    </svg>

                </button>

            </div>

            <div class="typing-indicator" id="typingIndicator" style="display: none;">

                <div class="typing-dots">

                    <span></span>

                    <span></span>

                    <span></span>

                </div>

                <span class="typing-text">L'assistant ECOSAVE écrit...</span>

            </div>

        </div>

    </div>

</div>



<style>

/* Professional Chatbot Styles */

.professional-chatbot {

    position: fixed;

    bottom: 30px;

    right: 30px;

    z-index: 9999;

}



.chatbot-toggle {

    position: relative;

    width: 60px;

    height: 60px;

    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);

    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    border: 2px solid rgba(255, 255, 255, 0.2);

}



.chatbot-toggle:hover {

    transform: scale(1.05);

    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.5);

}



.chatbot-icon {

    color: white;

    z-index: 2;

}



.chatbot-badge {

    position: absolute;

    bottom: -2px;

    right: -2px;

    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);

    color: white;

    font-size: 10px;

    font-weight: bold;

    padding: 4px 6px;

    border-radius: 12px;

    border: 2px solid white;

    z-index: 3;

}



.pulse-ring {

    position: absolute;

    top: -4px;

    left: -4px;

    right: -4px;

    bottom: -4px;

    border: 2px solid rgba(102, 126, 234, 0.3);

    border-radius: 50%;

    animation: pulse 2s infinite;

}



@keyframes pulse {

    0% {

        transform: scale(0.95);

        opacity: 1;

    }

    50% {

        transform: scale(1.1);

        opacity: 0.5;

    }

    100% {

        transform: scale(0.95);

        opacity: 1;

    }

}



.chatbot-window {

    position: absolute;

    bottom: 80px;

    right: 0;

    width: 380px;

    height: 520px;

    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);

    border-radius: 20px;

    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);

    display: none;

    flex-direction: column;

    overflow: hidden;

    border: 1px solid rgba(102, 126, 234, 0.1);

    backdrop-filter: blur(10px);

}



.chatbot-header {

    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    padding: 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    color: white;

}



.chatbot-header-content {

    display: flex;

    align-items: center;

    gap: 15px;

}



.chatbot-avatar {

    width: 50px;

    height: 50px;

    border-radius: 50%;

    background: rgba(255, 255, 255, 0.2);

    display: flex;

    align-items: center;

    justify-content: center;

    position: relative;

    overflow: hidden;

}



.avatar-gradient {

    position: absolute;

    top: -50%;

    left: -50%;

    width: 200%;

    height: 200%;

    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);

    animation: shimmer 3s infinite;

}



@keyframes shimmer {

    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }

    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }

}



.chatbot-title-section h3 {

    font-size: 18px;

    font-weight: 700;

    margin: 0;

    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

}



.chatbot-status {

    display: flex;

    align-items: center;

    gap: 8px;

    font-size: 12px;

    opacity: 0.9;

}



.status-dot {

    width: 8px;

    height: 8px;

    background: #4ade80;

    border-radius: 50%;

    animation: statusPulse 2s infinite;

}



@keyframes statusPulse {

    0%, 100% { opacity: 1; }

    50% { opacity: 0.5; }

}



.chatbot-close {

    background: rgba(255, 255, 255, 0.2);

    border: none;

    border-radius: 50%;

    width: 36px;

    height: 36px;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

    transition: all 0.3s ease;

    color: white;

}



.chatbot-close:hover {

    background: rgba(255, 255, 255, 0.3);

    transform: scale(1.1);

}



.chatbot-messages {

    flex: 1;

    padding: 20px;

    overflow-y: auto;

    background: #f8fafc;

}



.message {

    display: flex;

    gap: 12px;

    margin-bottom: 20px;

    animation: messageSlide 0.3s ease;

}



@keyframes messageSlide {

    from {

        opacity: 0;

        transform: translateY(10px);

    }

    to {

        opacity: 1;

        transform: translateY(0);

    }

}



.message-avatar {

    width: 36px;

    height: 36px;

    border-radius: 50%;

    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    color: white;

}



.user-message .message-avatar {

    background: linear-gradient(135deg, #10b981 0%, #059669 100%);

}



.message-content {

    flex: 1;

}



.message-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 8px;

}



.sender-name {

    font-weight: 600;

    color: #374151;

    font-size: 14px;

}



.message-time {

    font-size: 12px;

    color: #9ca3af;

}



.message-text {

    background: white;

    padding: 12px 16px;

    border-radius: 16px;

    color: #374151;

    line-height: 1.5;

    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

}



.user-message .message-text {

    background: linear-gradient(135deg, #10b981 0%, #059669 100%);

    color: white;

}



.quick-actions {

    padding: 15px 20px;

    background: white;

    border-top: 1px solid #e5e7eb;

    display: flex;

    gap: 10px;

    overflow-x: auto;

}



.quick-action-btn {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 6px;

    padding: 12px 16px;

    background: #f3f4f6;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    cursor: pointer;

    transition: all 0.3s ease;

    white-space: nowrap;

    min-width: 80px;

}



.quick-action-btn:hover {

    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    color: white;

    transform: translateY(-2px);

    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);

}



.btn-icon {

    font-size: 20px;

}



.btn-text {

    font-size: 12px;

    font-weight: 600;

}



.input-container {

    padding: 15px 20px;

    background: white;

    border-top: 1px solid #e5e7eb;

    display: flex;

    gap: 12px;

    align-items: center;

}



.chatbot-input {

    flex: 1;

    padding: 12px 16px;

    border: 2px solid #e5e7eb;

    border-radius: 25px;

    outline: none;

    font-size: 14px;

    transition: all 0.3s ease;

    background: #f9fafb;

}



.chatbot-input:focus {

    border-color: #667eea;

    background: white;

    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);

}



.send-button {

    width: 44px;

    height: 44px;

    border-radius: 50%;

    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

    border: none;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    cursor: pointer;

    transition: all 0.3s ease;

    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);

}



.send-button:hover {

    transform: scale(1.05);

    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);

}



.typing-indicator {

    padding: 15px 20px;

    background: white;

    border-top: 1px solid #e5e7eb;

    display: flex;

    align-items: center;

    gap: 12px;

}



.typing-dots {

    display: flex;

    gap: 4px;

}



.typing-dots span {

    width: 8px;

    height: 8px;

    background: #667eea;

    border-radius: 50%;

    animation: typing 1.4s infinite;

}



.typing-dots span:nth-child(2) {

    animation-delay: 0.2s;

}



.typing-dots span:nth-child(3) {

    animation-delay: 0.4s;

}



@keyframes typing {

    0%, 60%, 100% {

        transform: translateY(0);

        opacity: 0.4;

    }

    30% {

        transform: translateY(-10px);

        opacity: 1;

    }

}



.typing-text {

    font-size: 14px;

    color: #6b7280;

    font-style: italic;

}



/* Responsive Design */

@media (max-width: 768px) {

    .chatbot-window {

        width: 320px;

        height: 480px;

        right: -10px;

        bottom: 70px;

    }

    

    .quick-actions {

        justify-content: space-around;

    }

    

    .quick-action-btn {

        min-width: 70px;

        padding: 10px 12px;

    }

    

    .btn-icon {

        font-size: 16px;

    }

    

    .btn-text {

        font-size: 11px;

    }

}

</style>



<script>

// Professional Chatbot JavaScript

document.addEventListener('DOMContentLoaded', function() {

    const chatbotToggle = document.getElementById('chatbotToggle');

    const chatbotWindow = document.getElementById('chatbotWindow');

    const chatbotClose = document.getElementById('chatbotClose');

    const chatbotInput = document.getElementById('chatbotInput');

    const chatbotSend = document.getElementById('chatbotSend');

    const chatbotMessages = document.getElementById('chatbotMessages');

    const typingIndicator = document.getElementById('typingIndicator');

    

    // User data from PHP

    const userData = {

        name: '<?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>',

        regime: '<?php echo htmlspecialchars($user['regime_alimentaire'] ?: 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?>',

        objectif: '<?php echo htmlspecialchars($user['objectif_sante'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?>',

        niveau: '<?php echo htmlspecialchars($user['niveau_activite'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?>'

    };

    

    // Toggle chatbot

    chatbotToggle.addEventListener('click', function() {

        chatbotWindow.style.display = chatbotWindow.style.display === 'flex' ? 'none' : 'flex';

        if (chatbotWindow.style.display === 'flex') {

            chatbotInput.focus();

        }

    });

    

    // Close chatbot

    chatbotClose.addEventListener('click', function() {

        chatbotWindow.style.display = 'none';

    });

    

    // Quick action buttons

    document.querySelectorAll('.quick-action-btn').forEach(btn => {

        btn.addEventListener('click', function() {

            const action = this.getAttribute('data-action');

            handleQuickAction(action);

        });

    });

    

    // Send message

    function sendMessage() {

        const message = chatbotInput.value.trim();

        if (!message) return;

        

        // Add user message

        addMessage(message, 'user');

        

        // Clear input

        chatbotInput.value = '';

        

        // Show typing indicator

        typingIndicator.style.display = 'flex';

        

        // Generate response

        setTimeout(() => {

            typingIndicator.style.display = 'none';

            const response = generateResponse(message);

            addMessage(response, 'bot');

        }, 1500);

    }

    

    // Add message to chat

    function addMessage(text, sender) {

        const messageDiv = document.createElement('div');

        messageDiv.className = `message ${sender}-message`;

        

        const avatar = document.createElement('div');

        avatar.className = 'message-avatar';

        avatar.innerHTML = sender === 'bot' ? 

            '<svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/></svg>' :

            '<svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M20 21v-2H4v2l9-9 9 9z"/><path d="M17 7l-5 5-5-5h10z"/></svg>';

        

        const content = document.createElement('div');

        content.className = 'message-content';

        

        const header = document.createElement('div');

        header.className = 'message-header';

        header.innerHTML = `

            <span class="sender-name">${sender === 'bot' ? 'Assistant ECOSAVE Pro' : userData.name}</span>

            <span class="message-time">${new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</span>

        `;

        

        const textDiv = document.createElement('div');

        textDiv.className = 'message-text';

        textDiv.innerHTML = text;

        

        content.appendChild(header);

        content.appendChild(textDiv);

        messageDiv.appendChild(avatar);

        messageDiv.appendChild(content);

        

        chatbotMessages.appendChild(messageDiv);

        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

    }

    

    // Generate intelligent response

    function generateResponse(message) {

        const lowerMessage = message.toLowerCase();

        

        // Recettes

        if (lowerMessage.includes('recette') || lowerMessage.includes('cuisine') || lowerMessage.includes('plat')) {

            return generateRecipeResponse(lowerMessage);

        }

        

        // Sport

        if (lowerMessage.includes('sport') || lowerMessage.includes('exercice') || lowerMessage.includes('entraînement') || lowerMessage.includes('muscu')) {

            return generateSportResponse(lowerMessage);

        }

        

        // Objectifs

        if (lowerMessage.includes('objectif') || lowerMessage.includes('poids') || lowerMessage.includes('perte') || lowerMessage.includes('prise')) {

            return generateObjectiveResponse(lowerMessage);

        }

        

        // Conseils

        if (lowerMessage.includes('conseil') || lowerMessage.includes('astuce') || lowerMessage.includes('aide')) {

            return generateAdviceResponse(lowerMessage);

        }

        

        // Nutrition

        if (lowerMessage.includes('nutrition') || lowerMessage.includes('aliment') || lowerMessage.includes('manger')) {

            return generateNutritionResponse(lowerMessage);

        }

        

        // Default response

        return `Bonjour ${userData.name} ! Je suis votre assistant ECOSAVE Pro. Je peux vous aider avec :\n\n🥗 **Recettes personnalisées** selon votre régime ${userData.regime}\n💪 **Programmes sportifs** adaptés à votre niveau ${userData.niveau}\n🎯 **Stratégies** pour atteindre votre objectif "${userData.objectif}"\n💡 **Conseils** nutritionnels et bien-être\n\nPosez-moi une question spécifique pour une réponse personnalisée !`;

    }

    

    // Recipe responses

    function generateRecipeResponse(message) {

        // Détecter les mots-clés spécifiques dans le message

        let regimeType = userData.regime.toLowerCase();

        

        // Priorité aux mots-clés explicites dans le message

        if (message.includes('végan') || message.includes('vegan')) {

            regimeType = 'végétalien';

        } else if (message.includes('végétarien') || message.includes('végéta')) {

            regimeType = 'végétarien';

        } else if (message.includes('sans') || message.includes('normal')) {

            regimeType = 'sans';

        }

        

        const recipes = {

            'végétalien': [

                '� **Curry de Lentilles Végan**\nLentilles corail 200g + lait de coco 200ml + épinards 150g + riz complet 150g + épices curry\n\n📊 *Nutrition*: 480 calories, 24g protéines, 12g lipides\n⏰ *Préparation*: 35 minutes\n🌿 *Bénéfices*: 100% végan, riche en protéines végétales et fer',

                '� **Tacos Végétaliens**\nHaricots noirs 250g + maïs 100g + avocat 1/2 + tortillas complètes + sauce tahin-citron\n\n📊 *Nutrition*: 420 calories, 18g protéines\n⏰ *Préparation*: 25 minutes\n🌿 *Bénéfices*: Sans produits animaux, fibres et oméga-3',

                '🍜 **Bol Ramen Végan**\nNouilles soba 120g + tofu ferme 150g + champignons shiitaké 100g + bouillon miso\n\n📊 *Nutrition*: 380 calories, 22g protéines\n⏰ *Préparation*: 30 minutes\n🌿 *Bénéfices*: Complet en acides aminés essentiels'

            ],

            'végétarien': [

                '� **Buddha Bowl Végétarien**\nQuinoa 200g + pois chiches 150g + avocat 1/2 + betteraves rôties 100g + sauce tahin 2cs\n\n📊 *Nutrition*: 450 calories, 25g protéines, 15g lipides\n⏰ *Préparation*: 25 minutes\n🌿 *Bénéfices*: Riche en protéines végétales et oméga-3',

                '� **Pâtes Complètes Légumes**\nPâtes complètes 120g + courgettes 200g + tomates cerises 100g + basilic frais + parmesan 30g\n\n📊 *Nutrition*: 380 calories, 18g protéines\n⏰ *Préparation*: 20 minutes\n🌿 *Bénéfices*: Équilibré en glucides complexes',

                '🧀 **Quiche Légumes Fromage**\nPâte brisée + œufs 3 + lait végétal 200ml + épinards 200g + fromage chèvre 50g\n\n📊 *Nutrition*: 340 calories, 16g protéines\n⏰ *Préparation*: 40 minutes\n🌿 *Bénéfices*: Source de calcium et protéines'

            ],

            'sans': [

                '🥩 **Poulet Grillé Légumes**\nBlanc de poulet 200g + asperges 150g + poivrons 100g + citron\n\n📊 *Nutrition*: 380 calories, 35g protéines\n⏰ *Préparation*: 25 minutes\n🌿 *Bénéfices*: Riche en protéines maigres',

                '🐟 **Saumon Quinoa**\nSaumon 180g + quinoa 150g + brocolis 100g + amandes 20g\n\n📊 *Nutrition*: 450 calories, 32g protéines\n⏰ *Préparation*: 30 minutes\n🌿 *Bénéfices*: Excellent oméga-3 et protéines',

                '🍖 **Bœuf Bourguignon Allégé**\nBœuf maigre 200g + carottes 150g + champignons 100g + vin rouge 50ml\n\n📊 *Nutrition*: 420 calories, 30g protéines\n⏰ *Préparation*: 45 minutes\n🌿 *Bénéfices*: Riche en fer et protéines complètes'

            ]

        };

        

        const userRecipes = recipes[regimeType] || recipes['sans'];

        return userRecipes[Math.floor(Math.random() * userRecipes.length)];

    }

    

    // Sport responses

    function generateSportResponse(message) {

        const sportPrograms = {

            'débutant': [

                '💪 **Programme Débutant - Semaine 1**\n\n**Lundi**: Marche rapide 30min\n**Mercredi**: Musculation full body 30min\n**Vendredi**: Yoga 20min\n**Samedi**: Natation 30min\n\n📊 *Fréquence*: 4 séances/semaine\n⏰ *Durée totale*: 2h/semaine\n🎯 *Objectif*: Conditionnement de base',

                '🏃 **Programme Cardio Débutant**\n\n**Jours impairs**: Marche rapide 25min\n**Jours pairs**: Vélo elliptique 20min\n\n📊 *Intensité*: Modérée (60-70% FCmax)\n⏰ *Progression*: Augmenter de 5min chaque semaine\n🎯 *Objectif*: Améliorer endurance'

            ],

            'intermédiaire': [

                '🏋️ **Programme Intermédiaire - Split**\n\n**Lundi**: Pectoraux + Triceps\n**Mardi**: Dos + Biceps\n**Jeudi**: Jambes + Abdos\n**Samedi**: Épaules + Mollets\n\n📊 *Fréquence*: 4 séances/semaine\n⏰ *Durée*: 45-60min/séance\n🎯 *Objectif*: Hypertrophie et force',

                '🥊 **HIIT Intermédiaire**\n\n**Lundi**: Sprints 20min\n**Mercredi**: Circuit training 30min\n**Vendredi**: Tabata 15min\n\n📊 *Intensité*: Élevée (80-90% FCmax)\n⏰ *Ratio*: 1:2 travail:récupération\n🎯 *Objectif*: Performance et perte de poids'

            ],

            'avancé': [

                '🏆 **Programme Avancé - Double Split**\n\n**Matin**: Groupe musculaire principal\n**Soir**: Groupe musculaire secondaire\n\n📊 *Fréquence*: 6 séances/semaine\n⏰ *Durée*: 60-75min/séance\n🎯 *Objectif*: Performance maximale',

                '⚡ **Programme Athlétique**\n\n**Lundi**: Force explosive\n**Mercredi**: Endurance spécifique\n**Vendredi**: Mobilité et récupération\n**Samedi**: Compétition simulée\n\n📊 *Spécialisation*: Sport spécifique\n⏰ *Intensité*: Très élevée\n🎯 *Objectif*: Performance compétitive'

            ]

        };

        

        const userPrograms = sportPrograms[userData.niveau.toLowerCase()] || sportPrograms['débutant'];

        return userPrograms[Math.floor(Math.random() * userPrograms.length)];

    }

    

    // Objective responses

    function generateObjectiveResponse(message) {

        const objectives = {

            'perte de poids': [

                '🎯 **Stratégie Perte de Poids**\n\n**Déficit calorique**: 500-700 kcal/jour\n**Protéines**: 1.8-2.2g/kg poids corporel\n**Cardio**: 4-5 séances/semaine, 30-45min\n**Muscu**: 2-3 séances/semaine\n\n📊 *Perte attendue*: 0.5-1kg/semaine\n⏰ *Durée*: 12-16 semaines\n🎯 *Maintien*: Important après la perte',

                '🥗 **Plan Nutrition Perte de Poids**\n\n**Petit-déjeuner**: 300-400 kcal\n**Déjeuner**: 400-500 kcal\n**Collation**: 150-200 kcal\n**Dîner**: 300-400 kcal\n\n📊 *Total*: 1200-1500 kcal/jour\n🌿 *Conseil*: Boire 2-3L eau par jour\n🎯 *Suivi*: Pesée 3x/semaine'

            ],

            'prise de masse': [

                '💪 **Stratégie Prise de Masse**\n\n**Surplus calorique**: +300-500 kcal/jour\n**Protéines**: 2.2-2.5g/kg poids corporel\n**Muscu**: 4-5 séances/semaine\n**Cardio**: 2-3 séances/semaine, 20min\n\n📊 *Prise attendue*: 0.25-0.5kg/semaine\n⏰ *Durée*: 16-20 semaines\n🎯 *Qualité*: Prioriser masse grasse minimale',

                '🏋️ **Plan Nutrition Prise de Masse**\n\n**Petit-déjeuner**: 500-600 kcal\n**Déjeuner**: 600-700 kcal\n**Collation**: 300-400 kcal\n**Dîner**: 500-600 kcal\n\n📊 *Total*: 1900-2300 kcal/jour\n🌿 *Conseil*: Repas toutes les 3h\n🎯 *Supplémentation*: Créatine possible'

            ]

        };

        

        const userObjectives = objectives[userData.objectif.toLowerCase()] || objectives['perte de poids'];

        return userObjectives[Math.floor(Math.random() * userObjectives.length)];

    }

    

    // Advice responses

    function generateAdviceResponse(message) {

        return `💡 **Conseil Personnalisé pour ${userData.name}**\n\nBasé sur votre profil (${userData.regime}, ${userData.objectif}, ${userData.niveau}):\n\n🎯 **Priorité n°1**: Consistance dans vos efforts\n📊 **Indicateurs à suivre**: Poids, mensurations, photos\n🌿 **Hydratation**: 2-3L d'eau par jour minimum\n😴 **Sommeil**: 7-9h de qualité pour la récupération\n📈 **Progression**: Augmenter de 10% chaque semaine\n\nBesoin de conseils spécifiques ? Demandez-moi sur un domaine précis !`;

    }

    

    // Nutrition responses

    function generateNutritionResponse(message) {

        return `🥗 **Conseils Nutritionnels Personnalisés**\n\n**Pour votre régime ${userData.regime} et objectif ${userData.objectif}**:\n\n📊 **Macros recommandés**:\n• Protéines: 1.8-2.2g/kg\n• Glucides: 40-50% calories\n• Lipides: 20-30% calories\n\n⏰ **Timing des repas**: Toutes les 3-4h\n🌿 **Hydratation**: 2-3L/jour\n📈 **Supplémentation possible**: Vitamine D, Oméga-3\n\nQuestions sur un repas spécifique ou un aliment ?`;

    }

    

    // Handle quick actions

    function handleQuickAction(action) {

        const actionMessages = {

            'recette': 'Je vous propose des recettes adaptées à votre régime. Voulez-vous une recette rapide (max 30min) ou une recette complète ?',

            'sport': 'Je peux créer un programme sportif personnalisé. Quel type d\'entraînement vous intéresse : musculation, cardio, ou mixte ?',

            'conseil': 'Quels conseils vous intéressent le plus : nutrition, récupération, motivation, ou organisation ?',

            'objectif': 'Analysons ensemble votre objectif. Voulez-vous une stratégie hebdomadaire ou des conseils spécifiques ?'

        };

        

        addMessage(actionMessages[action], 'bot');

    }

    

    // Send message on button click

    chatbotSend.addEventListener('click', sendMessage);

    

    // Send message on Enter key

    chatbotInput.addEventListener('keypress', function(e) {

        if (e.key === 'Enter') {

            sendMessage();

        }

    });

});

</script>

</body>

</html>?php require __DIR__ . '/partials/footer.php'; ?>

