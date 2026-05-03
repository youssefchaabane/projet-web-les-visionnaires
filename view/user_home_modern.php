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
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 40px;
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
            <div class="profile-photo">
                <img id="profileImage" src="https://picsum.photos/seed/user<?php echo $userId; ?>/150/150.jpg" alt="Photo de profil">
                <div class="photo-overlay">
                    <span>📷</span>
                    <span>Changer la photo</span>
                </div>
            </div>
            <div class="user-welcome">
                <h1>🌱 Bonjour <?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>!</h1>
                <p>Bienvenue sur votre compte</p>
            </div>
        </div>
        <div class="user-badge">
            <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo date('d'); ?></div>
                <div class="stat-label">Jours ce mois</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">85%</div>
                <div class="stat-label">Objectifs atteints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">12</div>
                <div class="stat-label">Semaines actives</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">A+</div>
                <div class="stat-label">Score santé</div>
            </div>
        </div>
    </div>

    <!-- Navigation Icons -->
    <div class="navigation-icons">
        <a href="#" class="nav-icon">
            <div class="nav-icon-icon">🏠</div>
            <div class="nav-icon-label">Accueil</div>
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
                    <div class="card-icon">🎯</div>
                    Mes objectifs
                </div>
                <a href="profile.php" class="card-action">Voir tout</a>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Perte de poids</span>
                    <span>75%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 75%;"></div>
                </div>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Alimentation équilibrée</span>
                    <span>90%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 90%;"></div>
                </div>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Activité physique</span>
                    <span>60%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%;"></div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">📊</div>
                    Mon profil
                </div>
                <a href="profile.php" class="card-action">Modifier</a>
            </div>
            
            <div class="goal-item">
                <div class="goal-info">
                    <div class="goal-title">Régime alimentaire</div>
                    <div class="goal-date"><?php echo htmlspecialchars($user['regime_alimentaire'] ?: 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="goal-status active">Actif</div>
            </div>
            
            <div class="goal-item">
                <div class="goal-info">
                    <div class="goal-title">Objectif santé</div>
                    <div class="goal-date"><?php echo htmlspecialchars($user['objectif_sante'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="goal-status active">En cours</div>
            </div>
            
            <div class="goal-item">
                <div class="goal-info">
                    <div class="goal-title">Niveau d'activité</div>
                    <div class="goal-date"><?php echo htmlspecialchars($user['niveau_activite'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="goal-status completed">Défini</div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">💡</div>
                    Recommandations
                </div>
                <a href="#" class="card-action">Plus</a>
            </div>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">🥗</div>
                <div class="recommendation-content">
                    <div class="recommendation-title">Augmentez les légumes</div>
                    <div class="recommendation-text">Essayez d'ajouter 2 portions de légumes supplémentaires par jour pour atteindre vos objectifs.</div>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">💧</div>
                <div class="recommendation-content">
                    <div class="recommendation-title">Hydratation</div>
                    <div class="recommendation-text">Buvez au moins 2 litres d'eau par jour pour optimiser votre métabolisme.</div>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">🏃</div>
                <div class="recommendation-content">
                    <div class="recommendation-title">Activité physique</div>
                    <div class="recommendation-text">30 minutes de marche rapide quotidienne peuvent améliorer votre santé cardiovasculaire.</div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">🌱</div>
                    Actions écologiques
                </div>
                <a href="#" class="card-action">Voir tout</a>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Réduction des déchets</span>
                    <span>40%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 40%;"></div>
                </div>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Consommation locale</span>
                    <span>65%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 65%;"></div>
                </div>
            </div>
            
            <div class="progress-item">
                <div class="progress-label">
                    <span>Transport durable</span>
                    <span>80%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 80%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="team-section">
        <div class="team-header">
            <h2 class="team-title">Notre Équipe</h2>
            <p class="team-subtitle">Les membres qui ont rendu ce projet possible</p>
        </div>
        <div class="team-grid">
            <div class="team-member">
                <img src="https://picsum.photos/seed/member1/150/150.jpg" alt="Membre 1" class="member-photo">
                <h3 class="member-name">Alexandre Martin</h3>
                <p class="member-role">Développeur Frontend</p>
                <p class="member-bio">Spécialiste en design UX/UI et développement web moderne</p>
            </div>
            <div class="team-member">
                <img src="https://picsum.photos/seed/member2/150/150.jpg" alt="Membre 2" class="member-photo">
                <h3 class="member-name">Sophie Bernard</h3>
                <p class="member-role">Développeur Backend</p>
                <p class="member-bio">Expert en bases de données et architecture système</p>
            </div>
            <div class="team-member">
                <img src="https://picsum.photos/seed/member3/150/150.jpg" alt="Membre 3" class="member-photo">
                <h3 class="member-name">Lucas Dubois</h3>
                <p class="member-role">Chef de projet</p>
                <p class="member-bio">Coordination des équipes et gestion des délais</p>
            </div>
            <div class="team-member">
                <img src="https://picsum.photos/seed/member4/150/150.jpg" alt="Membre 4" class="member-photo">
                <h3 class="member-name">Emma Petit</h3>
                <p class="member-role">Designer UX</p>
                <p class="member-bio">Création d'interfaces intuitives et esthétiques</p>
            </div>
            <div class="team-member">
                <img src="https://picsum.photos/seed/member5/150/150.jpg" alt="Membre 5" class="member-photo">
                <h3 class="member-name">Thomas Leroy</h3>
                <p class="member-role">Développeur Full-stack</p>
                <p class="member-bio">Expert en technologies web et solutions intégrées</p>
            </div>
        </div>
    </div>
</div>

<!-- Chatbot Icon - Design amélioré -->
<div id="chatbot-icon" style="position:fixed;bottom:30px;right:30px;width:70px;height:70px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 25px rgba(16,185,129,0.4);z-index:1000;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);border:3px solid rgba(255,255,255,0.2);backdrop-filter:blur(10px);">
    <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
    <div style="position:absolute;bottom:0;right:0;width:20px;height:20px;background:#ef4444;border-radius:50%;border:2px solid white;display:flex;align-items:center;justify-content:center;">
        <span style="color:white;font-size:12px;font-weight:bold;">AI</span>
    </div>
</div>

<!-- Chatbot Window - Design moderne et amélioré -->
<div id="chatbot-window" style="position:fixed;bottom:110px;right:30px;width:400px;height:550px;background:linear-gradient(135deg,#ffffff,#f8fafc);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.15);display:none;z-index:999;flex-direction:column;border:1px solid rgba(16,185,129,0.1);backdrop-filter:blur(20px);">
    <!-- Header -->
    <div style="background:linear-gradient(135deg,#10b981,#059669);color:white;padding:20px;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.1),transparent);animation:shimmer 3s infinite;"></div>
        <div style="display:flex;align-items:center;gap:12px;z-index:2;">
            <div style="width:35px;height:35px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                </svg>
            </div>
            <div>
                <div style="font-weight:700;font-size:16px;text-shadow:0 2px 4px rgba(0,0,0,0.1);">Assistant ECOSAVE</div>
                <div style="font-size:12px;opacity:0.9;">• En ligne • Assistant intelligent</div>
            </div>
        </div>
        <button id="close-chatbot" style="background:none;border:none;color:white;cursor:pointer;font-size:24px;line-height:1;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all 0.3s ease;z-index:2;">&times;</button>
    </div>
    
    <!-- Messages -->
    <div id="chat-messages" style="flex:1;padding:20px;overflow-y:auto;background:linear-gradient(135deg,#f8fafc,#f1f5f9);position:relative;">
        <!-- Message de bienvenue amélioré -->
        <div style="background:linear-gradient(135deg,#e0f2fe,#f0f9ff);padding:16px;border-radius:12px;margin-bottom:16px;border-left:4px solid #10b981;box-shadow:0 4px 12px rgba(16,185,129,0.1);">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <div style="width:8px;height:8px;background:#10b981;border-radius:50%;animation:pulse 2s infinite;"></div>
                <span style="color:#10b981;font-weight:700;font-size:14px;">• Assistant ECOSAVE</span>
            </div>
            <p style="margin:0 0 8px;color:#1f2937;font-weight:500;line-height:1.5;">Bonjour <?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>! 👋</p>
            <p style="margin:0;color:#374151;line-height:1.6;">Je suis votre assistant personnel ECOSAVE. Je vois que votre régime est <strong style="color:#10b981;"><?php echo htmlspecialchars($user['regime_alimentaire'] ?: 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?></strong> et votre objectif santé est <strong style="color:#10b981;"><?php echo htmlspecialchars($user['objectif_sante'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
            <p style="margin:8px 0 0;color:#374151;line-height:1.6;">Je peux vous aider avec:</p>
            <ul style="margin:8px 0;padding-left:20px;color:#374151;line-height:1.6;">
                <li style="margin-bottom:4px;">🥗 Recettes personnalisées selon votre régime</li>
                <li style="margin-bottom:4px;">🏋‍♂️ Plans d'entraînement adaptés à votre niveau</li>
                <li style="margin-bottom:4px;">🌱 Conseils écologiques et anti-gaspillage</li>
                <li style="margin-bottom:4px;">📊 Suivi de vos objectifs santé</li>
                <li style="margin-bottom:4px;">💊 Informations nutritionnelles détaillées</li>
            </ul>
            <p style="margin:8px 0 0;color:#10b981;font-weight:600;">Posez-moi n'importe quelle question !</p>
        </div>
    </div>
    
    <!-- Input -->
    <div style="padding:20px;border-top:1px solid rgba(16,185,129,0.1);background:linear-gradient(135deg,#ffffff,#f8fafc);">
        <div style="display:flex;gap:12px;align-items:center;">
            <input id="chat-input" type="text" placeholder="💬 Posez-moi n'importe quelle question..." style="flex:1;padding:14px 18px;border:2px solid rgba(16,185,129,0.2);border-radius:25px;outline:none;font-size:15px;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.05);transition:all 0.3s ease;">
            <button id="send-message" style="background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;border-radius:50%;width:45px;height:45px;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 15px rgba(16,185,129,0.3);transition:all 0.3s ease;">
                <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
        <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
            <button class="quick-suggestion" data-suggestion="recette" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);padding:6px 12px;border-radius:15px;font-size:12px;cursor:pointer;transition:all 0.3s ease;">🥗 Recettes</button>
            <button class="quick-suggestion" data-suggestion="sport" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);padding:6px 12px;border-radius:15px;font-size:12px;cursor:pointer;transition:all 0.3s ease;">🏋‍♂️ Sport</button>
            <button class="quick-suggestion" data-suggestion="conseil" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);padding:6px 12px;border-radius:15px;font-size:12px;cursor:pointer;transition:all 0.3s ease;">💡 Conseils</button>
            <button class="quick-suggestion" data-suggestion="aide" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);padding:6px 12px;border-radius:15px;font-size:12px;cursor:pointer;transition:all 0.3s ease;">❓ Aide</button>
        </div>
    </div>
</div>

<style>
#chatbot-icon:hover {
    transform: scale(1.1);
    box-shadow:0 6px 25px rgba(16,185,129,0.4);
}

#chatbot-window {
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.1);
    }
}

.chat-message {
    margin-bottom:12px;
    max-width:80%;
}

.chat-user {
    background:#27ae60;
    color:white;
    padding:10px 14px;
    border-radius:18px 18px 4px 18px;
    margin-left:auto;
    text-align:right;
}

.chat-bot {
    background:#e8f5e8;
    color:#2b8a3e;
    padding:10px 14px;
    border-radius:18px 18px 18px 4px;
}

#chat-input:focus {
    border-color:#10b981;
    box-shadow:0 0 0 3px rgba(16,185,129,0.1);
}

#send-message:hover {
    transform: scale(1.05);
    box-shadow:0 6px 20px rgba(16,185,129,0.4);
}

.quick-suggestion:hover {
    background:#10b981;
    color:white;
    transform: translateY(-2px);
}
</style>

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

    // Gestion du changement de photo de profil
    const profilePhoto = document.querySelector('.profile-photo');
    const profileImage = document.getElementById('profileImage');
    const photoOverlay = document.querySelector('.photo-overlay');
    
    photoOverlay.addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
        input.click();
    });

    // Chatbot functionality
    const chatbotIcon = document.getElementById('chatbot-icon');
    const chatbotWindow = document.getElementById('chatbot-window');
    const closeChatbot = document.getElementById('close-chatbot');
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-message');
    const chatMessages = document.getElementById('chat-messages');
    
    // Toggle chatbot
    chatbotIcon.addEventListener('click', function() {
        chatbotWindow.style.display = chatbotWindow.style.display === 'flex' ? 'none' : 'flex';
        if (chatbotWindow.style.display === 'flex') {
            chatInput.focus();
        }
    });
    
    closeChatbot.addEventListener('click', function() {
        chatbotWindow.style.display = 'none';
    });
    
    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        // Add user message
        const userMsg = document.createElement('div');
        userMsg.className = 'chat-message chat-user';
        userMsg.innerHTML = message;
        chatMessages.appendChild(userMsg);
        
        // Clear input
        chatInput.value = '';
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Simulate bot response
        setTimeout(() => {
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-message chat-bot';
            botMsg.innerHTML = getBotResponse(message);
            chatMessages.appendChild(botMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 1000);
    }
    
    function getBotResponse(message) {
        const lowerMessage = message.toLowerCase();
        const userName = '<?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>';
        const userRegime = '<?php echo htmlspecialchars($user['regime_alimentaire'] ?: 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?>';
        const userObjectif = '<?php echo htmlspecialchars($user['objectif_sante'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?>';
        const userNiveau = '<?php echo htmlspecialchars($user['niveau_activite'] ?: 'Non défini', ENT_QUOTES, 'UTF-8'); ?>';
        
        // Système intelligent de traitement des questions
        if (lowerMessage.includes('régime') || lowerMessage.includes('aliment') || lowerMessage.includes('manger') || lowerMessage.includes('nutrition')) {
            const regimeResponses = [
                `Pour votre régime ${userRegime}, je recommande un apport quotidien de 1.5-2g de protéines par kg de poids corporel. Privilégiez les protéines maigres comme le poulet, le poisson, les légumineuses et les produits laitiers faibles en matières grasses.`,
                `Un régime ${userRegime} équilibré doit inclure 50-55% de glucides complexes (céréales complètes, légumes), 25-30% de protéines et 20-25% de bons gras (avocat, noix, huiles d'olive).`,
                `Pour optimiser votre régime ${userRegime}, consommez 2-3 portions de légumes verts par jour (épinards, brocolis, kale) riches en fer et vitamines. Complétez avec des agrumes pour la vitamine C.`,
                `Votre régime ${userRegime} peut être enrichi avec des super-aliments: spiruline (5g/jour), graines de chia (15g/jour), baies de goji (30g/jour). Ils sont excellents pour les apports en antioxydants.`,
                `Planifiez vos repas avec la méthode du batch cooking: préparez 4-5 repas le dimanche pour toute la semaine. Cela garantit une alimentation équilibrée même les jours chargés.`
            ];
            return regimeResponses[Math.floor(Math.random() * regimeResponses.length)];
        }
        
        if (lowerMessage.includes('objectif') || lowerMessage.includes('santé') || lowerMessage.includes('but') || lowerMessage.includes('goal')) {
            const objectifResponses = [
                `Pour atteindre votre objectif "${userObjectif}", fixez-vous des objectifs SMART: Spécifiques, Mesurables, Atteignables, Réalistes et Temporellement définis. Exemple: "Perdre 2kg en 6 semaines".`,
                `Votre objectif "${userObjectif}" est excellent! Suivez la règle du 80/20: 80% du temps, suivez votre régime à la lettre; 20% du temps, accordez-vous des plaisirs modérés.`,
                `Pour "${userObjectif}", créez un journal de suivi: pesez-vous 3 fois par semaine, prenez des photos mensuelles, et mesurez vos tours de taille. La progression motive!`,
                `L'atteinte de votre objectif "${userObjectif}" passe par la consistance: 30 minutes d'activité modérée 5-6 jours par semaine + 7-8 heures de sommeil de qualité.`,
                `Décomposez votre objectif "${userObjectif}" en mini-objectifs hebdomadaires. Célébrez chaque succès pour maintenir votre motivation à long terme.`
            ];
            return objectifResponses[Math.floor(Math.random() * objectifResponses.length)];
        }
        
        if (lowerMessage.includes('sport') || lowerMessage.includes('activité') || lowerMessage.includes('exercice') || lowerMessage.includes('entraînement')) {
            const sportResponses = [
                `Avec votre niveau ${userNiveau}, je recommande un programme d'entraînement progressif: commencez par 3 séances de 30 minutes par semaine, puis augmentez à 4-5 séances après 4 semaines.`,
                `Pour votre niveau ${userNiveau}, optez pour le fractionné: 1 minute d'effort intense suivie de 2 minutes de récupération active. C'est plus efficace que le cardio continu!`,
                `Votre niveau ${userNiveau} est idéal pour la musculation: 2-3 séances par semaine en full body (squats, développés couchés, rowing, tractions). Focus sur la progression progressive.`,
                `Pour ${userNiveau}, variez les activités: marche rapide 30min + yoga 20min, 2 fois par semaine. Cela prévient les blessures et maintient la motivation.`,
                `Programme pour ${userNiveau}: Lundi (cardio), Mercredi (musculation), Vendredi (fractionné), Samedi (randonnée légère). Adaptez l'intensité selon votre forme du jour.`
            ];
            return sportResponses[Math.floor(Math.random() * sportResponses.length)];
        }
        
        if (lowerMessage.includes('recette') || lowerMessage.includes('cuisine') || lowerMessage.includes('plat')) {
            const recetteResponses = [
                `Recette parfaite pour ${userRegime}: Buddha bowl quinoa (200g) + pois chiches (150g) + avocat (1/2) + betteraves rôties (100g) + sauce tahin (2cs). 450 calories, 25g protéines.`,
                `Smoothie énergisant: 1 banane + 1 poignée épinards + 1 cs beurre cacahuète + 200ml lait végétal + 1 cs graines chia. 320 calories, 12g protéines.`,
                `Salade complète: quinoa cuit (180g) + lentilles corail (120g) + concombre (100g) + tomates (150g) + feta (50g) + vinaigrette citron. 420 calories, 18g protéines.`,
                `Petit-déjeuner protéiné: flocons d'avoine (60g) + whey protein (25g) + fruits rouges (100g) + noix (20g). 380 calories, 28g protéines.`
            ];
            return recetteResponses[Math.floor(Math.random() * recetteResponses.length)];
        }
        
        if (lowerMessage.includes('poids') || lowerMessage.includes('perdre') || lowerMessage.includes('mincir') || lowerMessage.includes('maigrir')) {
            const poidsResponses = [
                `Pour une perte de poids saine, visez 0.5-1kg par semaine maximum. Au-delà, vous risquez de perdre du muscle plutôt que de la graisse.`,
                `Le secret minceur: mangez lentement (20-30 minutes par repas), dans une assiette (pas devant TV), et arrêtez-vous quand vous êtes à 80% rassasié.`,
                `Optimisez votre métabolisme: protéines à chaque repas (20-30g), fibres (25-30g par jour), et eau (2-3L par jour). Évitez les sucres rapides après 19h.`,
                `Le jeûne intermittent 16/8 peut accélérer la perte de poids: mangez entre 12h-20h, jeûnez de 20h-12h. Buvez eau, thé vert ou café sans sucre.`,
                `HIIT (High Intensity Interval Training): 20 minutes d'exercice intense brûle plus de calories que 45 minutes de cardio modéré. 2-3 fois par semaine suffisent.`
            ];
            return poidsResponses[Math.floor(Math.random() * poidsResponses.length)];
        }
        
        if (lowerMessage.includes('énergie') || lowerMessage.includes('fatigue') || lowerMessage.includes('tired') || lowerMessage.includes('épuisé')) {
            const energieResponses = [
                `Pour combattre la fatigue, optimisez votre fer: viandes rouges maigres, épinards, lentilles, graines de citrouille. Associez avec vitamine C (agrumes, poivrons) pour une absorption maximale.`,
                `Le magnésium est essentiel contre la fatigue: 300-400mg par jour. Sources: noix, graines, chocolat noir (>70%), légumes verts à feuilles.`,
                `La vitamine B12 est cruciale pour l'énergie: 2.4μg par jour. Si vous êtes végétarien, considérez la supplementation ou les aliments enrichis.`,
                `Le sommeil est fondamental: 7-9 heures par nuit, avec un horaire régulier. La mélatonine produite entre 23h-2h optimise la récupération.`,
                `Évitez les pics glycémiques: privilégiez les glucides complexes (céréales complètes, légumes) aux sucres rapides pour une énergie stable toute la journée.`
            ];
            return energieResponses[Math.floor(Math.random() * energieResponses.length)];
        }
        
        if (lowerMessage.includes('conseil') || lowerMessage.includes('aide') || lowerMessage.includes('astuce') || lowerMessage.includes('tip')) {
            const conseilResponses = [
                `Conseil nutrition: préparez vos repas pour la semaine le dimanche (batch cooking). Gagnez 5-10 heures par semaine et mangez plus sainement.`,
                `Astuce minceur: buvez un grand verre d'eau 15 minutes avant chaque repas. Souvent la faim est en fait une soif.`,
                `Pour la motivation: fixez-vous des défis hebdomadaires. "Cette semaine: 5 fruits différents", "Essayer 1 nouvelle recette", etc.`,
                `Écoutez vos signaux de faim et de satiété. Mangez quand vous avez faim (estomac qui gargouille), arrêtez quand vous êtes rassasié.`,
                `Variez vos sources de protéines: poulet, poisson, œufs, légumineuses, produits laitiers. Chaque source apporte des acides aminés différents.`
            ];
            return conseilResponses[Math.floor(Math.random() * conseilResponses.length)];
        }
        
        if (lowerMessage.includes('bonjour') || lowerMessage.includes('salut') || lowerMessage.includes('hello')) {
            const salutationsResponses = [
                `Bonjour ${userName}! Comment allez-vous aujourd'hui? Je suis votre assistant ECOSAVE, prêt à vous aider avec vos objectifs santé et votre bien-être.`,
                `Salut ${userName}! Ravie de vous voir sur votre espace ECOSAVE. Comment puis-je vous accompagner dans votre parcours aujourd'hui?`,
                `Hello ${userName}! Votre profil montre que vous suivez un régime ${userRegime} avec l'objectif "${userObjectif}". Comment se passe votre journée?`,
                `Bonjour ${userName}! Je suis là pour vous aider. Dites-moi ce qui vous intéresse: nutrition, sport, recettes, ou bien-être?`
            ];
            return salutationsResponses[Math.floor(Math.random() * salutationsResponses.length)];
        }
        
        if (lowerMessage.includes('merci') || lowerMessage.includes('thanks')) {
            const merciResponses = [
                `Avec plaisir ${userName}! C'est un honneur de vous accompagner dans votre parcours ECOSAVE. N'hésitez pas si vous avez d'autres questions.`,
                `De rien ${userName}! Votre succès me motive. Continuez comme ça, vous êtes sur la excellente voie!`,
                `Je suis là pour ça ${userName}! Chaque question vous aide à progresser. Continuez votre excellent travail!`,
                `Merci à vous ${userName}! Votre détermination est impressionnante. Ensemble, nous atteindrons vos objectifs!`
            ];
            return merciResponses[Math.floor(Math.random() * merciResponses.length)];
        }
        
        // Réponses intelligentes pour questions générales
        const generalResponses = [
            `Bonjour ${userName}! Je suis votre assistant nutritionnel ECOSAVE spécialisé. Avec votre profil (${userRegime}, objectif "${userObjectif}", niveau ${userNiveau}), je peux vous créer:\n\n📋 Plans d'action personnalisés\n🥗 Recettes adaptées à votre régime\n🏋‍♂️ Programmes d'entraînement progressifs\n📊 Stratégies de suivi et motivation\n\nPosez-moi votre question spécifique et je vous donnerai une réponse détaillée!`,
            
            `Intéressant ${userName}! Je suis là pour vous aider concrètement. Dites-moi précisément ce que vous voulez:\n\n• "Donne-moi un plan d'entraînement"\n• "Quelles recettes pour ${userRegime}?"\n• "Comment perdre du poids avec mon niveau ${userNiveau}?"\n• "Menu semaine pour objectif ${userObjectif}"\n\nPlus votre question est précise, plus ma réponse sera utile!`,
            
            `Bonjour ${userName}! Je suis votre coach virtuel ECOSAVE. Je peux vous aider avec:\n\n🎯 Objectif: "${userObjectif}"\n🥗 Régime: ${userRegime}\n🏋‍♂️ Niveau: ${userNiveau}\n\nQuelle est votre question précise? Plans d'entraînement, recettes, stratégies de poids, conseils nutritionnels, ou suivi de progression?`,
            
            `${userName}! Je suis spécialisé en nutrition sportive et bien-être. Posez-moi vos questions précises:\n\n• "Programme d'entraînement pour ${userNiveau}"\n• "Recettes ${userRegime} pour ${userObjectif}"\n• "Combien de calories par jour pour mon objectif?"\n• "Plan repas semaine pour perte de poids"\n• "Exercices spécifiques pour mon niveau"\n\nJe vous donnerai des réponses détaillées et actionnables!`,
            
            `Excellent ${userName}! Je suis votre assistant ECOSAVE. Pour vous aider efficacement, posez-moi des questions spécifiques comme:\n\n• "Plan entraînement semaine"\n• "Menu quotidien calories"\n• "Exercices pour débutant"\n• "Recettes protéinées"\n• "Stratégie perte poids"\n\nPlus vous êtes précis, plus ma réponse sera personnalisée et utile!`,
            
            `Bonjour ${userName}! Je suis là pour vous guider concrètement. Dites-moi exactement ce que vous voulez savoir:\n\n📝 Exemples de questions précises:\n• "Donne-moi un programme d'entraînement"\n• "Quoi manger pour perdre du poids?"\n• "Menu semaine pour mon régime ${userRegime}"\n• "Exercices pour mon niveau ${userNiveau}"\n\nJe répondrai avec des plans d'action détaillés!`
        ];
        
        return generalResponses[Math.floor(Math.random() * generalResponses.length)];
    }
    
    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Gestionnaires d'événements pour les boutons de suggestion rapide
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
