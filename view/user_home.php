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

// Handle User Allergies Association/Dissociation
$pdo = config::getConnexion();
$msgSuccess = '';
$msgError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_allergy'])) {
        $actionAllergy = $_POST['action_allergy'];
        if ($actionAllergy === 'add') {
            $idAllergie = (int) ($_POST['id_allergie'] ?? 0);
            if ($idAllergie > 0) {
                $chk = $pdo->prepare('SELECT COUNT(*) FROM utilisateur_allergie WHERE id_user = ? AND id_allergie = ?');
                $chk->execute([$userId, $idAllergie]);
                if ((int) $chk->fetchColumn() === 0) {
                    $ins = $pdo->prepare('INSERT INTO utilisateur_allergie (id_user, id_allergie) VALUES (?, ?)');
                    if ($ins->execute([$userId, $idAllergie])) {
                        $msgSuccess = "L'allergie a été ajoutée avec succès à votre profil !";
                    } else {
                        $msgError = "Une erreur est survenue lors de l'ajout.";
                    }
                } else {
                    $msgError = "Cette allergie est déjà enregistrée dans votre profil.";
                }
            } else {
                $msgError = "Veuillez sélectionner une allergie valide.";
            }
        } elseif ($actionAllergy === 'delete') {
            $idAllergie = (int) ($_POST['id_allergie'] ?? 0);
            if ($idAllergie > 0) {
                $del = $pdo->prepare('DELETE FROM utilisateur_allergie WHERE id_user = ? AND id_allergie = ?');
                if ($del->execute([$userId, $idAllergie])) {
                    $msgSuccess = "L'allergie a été retirée de votre profil.";
                } else {
                    $msgError = "Une erreur est survenue lors de la suppression.";
                }
            }
        }
    }
}

// Fetch user associated allergies
$stmtUser = $pdo->prepare('SELECT a.* FROM allergie a INNER JOIN utilisateur_allergie ua ON a.id_allergie = ua.id_allergie WHERE ua.id_user = ? ORDER BY a.nom ASC');
$stmtUser->execute([$userId]);
$userAllergies = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available allergies excluding already associated ones
$stmtAll = $pdo->prepare('SELECT a.id_allergie, a.nom FROM allergie a WHERE a.id_allergie NOT IN (SELECT ua.id_allergie FROM utilisateur_allergie ua WHERE ua.id_user = ?) ORDER BY a.nom ASC');
$stmtAll->execute([$userId]);
$availableAllergies = $stmtAll->fetchAll(PDO::FETCH_ASSOC);




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


.allergy-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.15));
    border: 1px solid rgba(239, 68, 68, 0.25);
    color: #ef4444;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    margin: 6px;
    transition: all 0.25s ease;
}

.allergy-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.2));
}

.allergy-badge form {
    display: inline-flex;
    margin-left: 10px;
}

.allergy-delete-btn {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 16px;
    font-weight: 700;
    padding: 0;
    line-height: 1;
    transition: transform 0.2s;
}

.allergy-delete-btn:hover {
    transform: scale(1.25);
    color: #dc2626;
}

.allergy-add-form {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    width: 100%;
}

.allergy-select {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
    background: #fff;
    color: #334155;
}

.allergy-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.allergy-btn-submit {
    padding: 12px 24px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.allergy-btn-submit:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.allergy-alert {
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.allergy-alert-success {
    background: #dcfce7;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.allergy-alert-error {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

</style>



<div class="dashboard-container">

    <div class="dashboard-header">

        <div class="user-profile">

            <div class="user-welcome">

                <h1 data-translate="bonjour_utilisateur">🌱 Bonjour <?php echo htmlspecialchars($user['nom_prenom'] ?: 'Utilisateur', ENT_QUOTES, 'UTF-8'); ?>!</h1>

                <p data-translate="bienvenue_compte">Bienvenue sur votre compte</p>

            </div>

        </div>

        <div class="user-badge">

            <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?>

        </div>

        

            </div>



    <!-- Navigation Icons -->

    <div class="navigation-icons">

        <a href="publications_client.php" class="nav-icon">

            <div class="nav-icon-icon">📝</div>

            <div class="nav-icon-label" data-translate="publication">Publication</div>

        </a>

        <a href="#" class="nav-icon">

            <div class="nav-icon-icon">🥗</div>

            <div class="nav-icon-label" data-translate="carnet_recettes">Recettes</div>

        </a>

        <a href="stock_client.php" class="nav-icon">

            <div class="nav-icon-icon">📦</div>

            <div class="nav-icon-label" data-translate="stock">Stock</div>

        </a>

        <a href="allergies_client.php" class="nav-icon">

            <div class="nav-icon-icon">🚫</div>

            <div class="nav-icon-label" data-translate="allergies">Allergies</div>

        </a>

        <a href="empreinte_client.php" class="nav-icon">

            <div class="nav-icon-icon">🌍</div>

            <div class="nav-icon-label" data-translate="empreinte">Empreinte</div>

        </a>

        <a href="recettes_client.php" class="nav-icon">

            <div class="nav-icon-icon">🍽️</div>

            <div class="nav-icon-label" data-translate="recettes">Recettes</div>

        </a>

    </div>



    <div class="dashboard-content">

        <div class="content-card">

            <div class="card-header">

                <div class="card-title">

                    <div class="card-icon">👤</div>

                    <span data-translate="mes_informations">Mes informations</span>

                </div>

                <a href="profile.php" class="card-action" data-translate="modifier">Modifier</a>

            </div>

            

            <div class="info-item">

                <div class="info-icon">📧</div>

                <div class="info-content">

                    <div class="info-label" data-translate="email">Email</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">👤</div>

                <div class="info-content">

                    <div class="info-label" data-translate="nom_complet">Nom complet</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['nom_prenom'], ENT_QUOTES, 'UTF-8'); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">🎂</div>

                <div class="info-content">

                    <div class="info-label" data-translate="date_inscription">Date d'inscription</div>

                    <div class="info-value"><?php echo date('d/m/Y', strtotime($user['date_creation'] ?? 'now')); ?></div>

                </div>

            </div>

            

            <div class="info-item">

                <div class="info-icon">🏷️</div>

                <div class="info-content">

                    <div class="info-label" data-translate="role">Rôle</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></div>

        </div>

        <!-- CARD: MES ALLERGIES -->
        <div class="content-card" id="allergies-section">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">🚫</div>
                    <span data-translate="mes_allergies">Mes allergies</span>
                </div>
            </div>

            <?php if ($msgSuccess !== ''): ?>
                <div class="allergy-alert allergy-alert-success">
                    <span>✅</span> <?php echo htmlspecialchars($msgSuccess, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($msgError !== ''): ?>
                <div class="allergy-alert allergy-alert-error">
                    <span>⚠️</span> <?php echo htmlspecialchars($msgError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 25px;">
                <p style="font-size: 14px; color: #64748b; margin-bottom: 15px;">Configurez et gérez les allergies enregistrées dans votre profil personnel pour bénéficier d'un suivi sur mesure.</p>
                
                <div style="display: flex; flex-wrap: wrap; margin: -6px;">
                    <?php if (empty($userAllergies)): ?>
                        <p style="font-size: 14px; font-style: italic; color: #94a3b8; padding: 15px; text-align: center; width: 100%;">Aucune allergie enregistrée. Restez en sécurité !</p>
                    <?php else: ?>
                        <?php foreach ($userAllergies as $all): ?>
                            <div class="allergy-badge">
                                🤧 <?php echo htmlspecialchars($all['nom'], ENT_QUOTES, 'UTF-8'); ?>
                                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment retirer cette allergie de votre profil ?');">
                                    <input type="hidden" name="action_allergy" value="delete">
                                    <input type="hidden" name="id_allergie" value="<?php echo $all['id_allergie']; ?>">
                                    <button type="submit" class="allergy-delete-btn" title="Retirer">&times;</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Formulaire d'ajout d'allergie -->
            <form method="POST" action="" class="allergy-add-form" style="border-top: 2px solid #f1f5f9; padding-top: 20px;">
                <input type="hidden" name="action_allergy" value="add">
                <select name="id_allergie" class="allergy-select" required>
                    <option value="">-- Ajouter une allergie... --</option>
                    <?php foreach ($availableAllergies as $all): ?>
                        <option value="<?php echo $all['id_allergie']; ?>"><?php echo htmlspecialchars($all['nom'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="allergy-btn-submit">Associer</button>
            </form>
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

    let historyLoaded = false;

    // Toggle chatbot and load history
    chatbotToggle.addEventListener('click', function() {
        chatbotWindow.style.display = chatbotWindow.style.display === 'flex' ? 'none' : 'flex';
        if (chatbotWindow.style.display === 'flex') {
            chatbotInput.focus();
            if (!historyLoaded) {
                loadHistory();
            }
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

    // Load Chat History from the database
    function loadHistory() {
        typingIndicator.style.display = 'flex';
        fetch('chatbot_api.php?action=history')
        .then(response => response.json())
        .then(data => {
            typingIndicator.style.display = 'none';
            if (data.success && data.history && data.history.length > 0) {
                // Clear initial static message before rendering historical ones
                chatbotMessages.innerHTML = '';
                data.history.forEach(msg => {
                    const sender = msg.sender === 'user' ? 'user' : 'bot';
                    const formatted = sender === 'bot' ? formatMarkdown(msg.message) : msg.message;
                    addMessage(formatted, sender);
                });
                historyLoaded = true;
            }
        })
        .catch(err => {
            typingIndicator.style.display = 'none';
            console.error('Erreur historique:', err);
        });
    }

    // Send message asynchronously to the backend
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (!message) return;
        
        // Add user message to UI
        addMessage(message, 'user');
        
        // Clear input
        chatbotInput.value = '';
        
        // Show typing indicator
        typingIndicator.style.display = 'flex';
        
        // Fetch from Azure OpenAI backend API
        fetch('chatbot_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            typingIndicator.style.display = 'none';
            if (data.success) {
                const formattedResponse = formatMarkdown(data.response);
                addMessage(formattedResponse, 'bot');
            } else if (data.error) {
                addMessage(`❌ Erreur : ${data.error}`, 'bot');
            } else {
                addMessage("❌ Une erreur inconnue s'est produite.", 'bot');


            }
        })
        .catch(err => {
            typingIndicator.style.display = 'none';
            addMessage("❌ Impossible de contacter le serveur ECOSAVE Pro. Veuillez réessayer.", 'bot');
            console.error(err);
        });
    }


            addMessage("❌ Impossible de contacter le serveur ECOSAVE Pro. Veuillez réessayer.", 'bot');
            console.error(err);
        });
    }


    // Add message to chat UI
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

    // Simple yet powerful markdown-to-HTML formatter
    function formatMarkdown(text) {
        if (!text) return '';
        let html = text;
        
        // Escape HTML to prevent XSS (but allow safe formatting tags)
        html = html
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Headers
        html = html.replace(/^### (.*?)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.*?)$/gm, '<h2>$1</h2>');
        html = html.replace(/^# (.*?)$/gm, '<h1>$1</h1>');

        // Bold
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Italic
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Lists
        html = html.replace(/^\s*[-*]\s+(.*?)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*?<\/li>)+/gs, '<ul>$&</ul>');

        // Paragraphs / Newlines
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    // Handle quick actions dynamically with real AI queries
    function handleQuickAction(action) {
        const actionMessages = {
            'recette': 'Propose-moi une recette saine adaptée à mon profil.',
            'sport': 'Crée-moi un programme sportif rapide et adapté.',
            'conseil': 'Donne-moi des conseils personnalisés pour ma routine.',
            'objectif': 'Quelle est la meilleure stratégie pour atteindre mon objectif ?'
        };
        const prompt = actionMessages[action];
        chatbotInput.value = prompt;
        sendMessage();
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

</html>
<?php require __DIR__ . '/partials/footer.php'; ?>

