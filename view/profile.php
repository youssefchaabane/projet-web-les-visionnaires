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

$pageTitle = 'Mon profil ECOSAVE';
require __DIR__ . '/partials/header.php';
?>

<div class="crud-card" style="max-width:960px;margin:0 auto;">
    <div style="display:grid;gap:18px;">
        <section style="background:linear-gradient(135deg,#2b8a3e,#16a085);color:#fff;padding:24px;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.12);">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                <div>
                    <p style="margin:0 0 10px;font-size:14px;letter-spacing:.3px;text-transform:uppercase;opacity:.8;">Profil utilisateur</p>
                    <h1 style="font-size:28px;line-height:1.1;margin:0;"><?php echo htmlspecialchars($user['nom_prenom'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p style="margin:14px 0 0;font-size:15px;max-width:620px;">Gérez vos informations personnelles et vos préférences alimentaires.</p>
                </div>
                <div style="min-width:180px;text-align:right;">
                    <span style="display:inline-block;padding:10px 16px;border-radius:999px;background:rgba(255,255,255,.18);font-weight:700;letter-spacing:.4px;text-transform:uppercase;font-size:12px;"><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
        </section>

        <section style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.08);">
            <h2 style="margin:0 0 20px;color:#2b8a3e;">Informations personnelles</h2>
            <div style="display:grid;gap:16px;">
                <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;padding:12px 0;border-bottom:1px solid #eee;">
                    <span style="font-weight:600;color:#666;">Email:</span>
                    <span><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;padding:12px 0;border-bottom:1px solid #eee;">
                    <span style="font-weight:600;color:#666;">Régime:</span>
                    <span><?php echo htmlspecialchars($user['regime'] ?? 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;padding:12px 0;">
                    <span style="font-weight:600;color:#666;">Statut:</span>
                    <span style="color:<?php echo $user['est_actif'] ? '#27ae60' : '#e74c3c'; ?>;"><?php echo $user['est_actif'] ? 'Actif' : 'Inactif'; ?></span>
                </div>
            </div>
        </section>

        <section style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.08);">
            <h2 style="margin:0 0 20px;color:#2b8a3e;">Objectifs alimentaires</h2>
            <div style="background:#f8f9fa;padding:16px;border-radius:8px;border-left:4px solid #27ae60;">
                <p style="margin:0;color:#666;">Votre régime actuel: <strong><?php echo htmlspecialchars($user['regime'] ?? 'Non défini', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                <p style="margin:8px 0 0;color:#666;">Recommandations personnalisées basées sur vos préférences.</p>
            </div>
        </section>
    </div>
</div>

<!-- Chatbot Icon -->
<div id="chatbot-icon" style="position:fixed;bottom:30px;right:30px;width:60px;height:60px;background:linear-gradient(135deg,#27ae60,#2ecc71);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 20px rgba(39,174,96,0.3);z-index:1000;transition:all 0.3s ease;">
    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window" style="position:fixed;bottom:100px;right:30px;width:350px;height:450px;background:white;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.12);display:none;z-index:999;flex-direction:column;">
    <!-- Header -->
    <div style="background:linear-gradient(135deg,#27ae60,#2ecc71);color:white;padding:16px;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:10px;">
            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
            </svg>
            <span style="font-weight:600;">Assistant ECOSAVE</span>
        </div>
        <button id="close-chatbot" style="background:none;border:none;color:white;cursor:pointer;font-size:20px;line-height:1;">&times;</button>
    </div>
    
    <!-- Messages -->
    <div id="chat-messages" style="flex:1;padding:16px;overflow-y:auto;background:#fafafa;">
        <div style="background:#e8f5e8;padding:12px;border-radius:8px;margin-bottom:12px;">
            <p style="margin:0;color:#2b8a3e;font-weight:500;">Bonjour <?php echo htmlspecialchars($user['nom_prenom'], ENT_QUOTES, 'UTF-8'); ?>!</p>
            <p style="margin:8px 0 0;color:#666;">Je vois que vous êtes sur votre profil ECOSAVE. Votre régime est <strong><?php echo htmlspecialchars($user['regime'] ?? 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?></strong>, comment puis-je vous aider aujourd'hui ?</p>
        </div>
    </div>
    
    <!-- Input -->
    <div style="padding:16px;border-top:1px solid #eee;">
        <div style="display:flex;gap:8px;">
            <input id="chat-input" type="text" placeholder="Tapez votre message..." style="flex:1;padding:10px;border:1px solid #ddd;border-radius:20px;outline:none;">
            <button id="send-message" style="background:#27ae60;color:white;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
#chatbot-icon:hover {
    transform: scale(1.1);
    box-shadow:0 6px 25px rgba(39,174,96,0.4);
}

#chatbot-window {
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
    border-radius:18px;
    margin-left:auto;
    text-align:right;
}

.chat-bot {
    background:#e8f5e8;
    color:#2b8a3e;
    padding:10px 14px;
    border-radius:18px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
        
        if (lowerMessage.includes('régime') || lowerMessage.includes('aliment')) {
            return 'Votre régime actuel est <strong><?php echo htmlspecialchars($user['regime'] ?? 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?></strong>. Je peux vous aider avec des recettes et recommandations adaptées.';
        } else if (lowerMessage.includes('recette')) {
            return 'Je peux vous suggérer des recettes adaptées à votre régime. Voulez-vous des recettes pour le petit-déjeuner, le déjeuner ou le dîner ?';
        } else if (lowerMessage.includes('objectif')) {
            return 'Vos objectifs sont personnalisés selon votre régime. Je peux vous aider à suivre vos progrès et ajuster votre plan alimentaire.';
        } else {
            return 'Je suis là pour vous aider avec votre profil ECOSAVE. Demandez-moi des conseils sur votre régime, des recettes ou vos objectifs alimentaires !';
        }
    }
    
    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
