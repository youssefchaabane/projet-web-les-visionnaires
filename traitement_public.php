<?php
session_start();
require_once __DIR__ . '/app/models/Traitement.php';
require_once __DIR__ . '/app/controllers/TraitementController.php';

$controller = new TraitementController();
$action = isset($_GET['action']) ? $_GET['action'] : 'accueil';
$data = [];

switch ($action) {
    case 'detail':
        $id = intval($_GET['id'] ?? 0);
        $data = $controller->afficherDetailTraitement($id);
        if (isset($data['erreur'])) $action = 'accueil';
        break;
    case 'rechercher':
        $terme = $_GET['q'] ?? '';
        $data = strlen(trim($terme)) > 0 ? $controller->rechercherTraitements($terme) : $controller->afficherListePublique();
        break;
    case 'traitements':
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $data = $controller->afficherListePublique($page);
        break;
    default:
        $data = $controller->afficherListePublique();
        $action = 'accueil';
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitements - Managedical</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f4f9f4; }
        header { background-color: #2e7d32; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        nav a:hover { color: #a5d6a7; }
        .hero { text-align: center; padding: 80px 20px; background: linear-gradient(to right, #66bb6a, #a5d6a7); color: white; }
        .hero h1 { font-size: 40px; margin-bottom: 10px; }
        .hero p { font-size: 18px; margin-bottom: 20px; }
        .hero button { padding: 12px 25px; border: none; background-color: white; color: #2e7d32; font-size: 16px; cursor: pointer; border-radius: 5px; font-weight: bold; }
        .search-section { padding: 40px; text-align: center; }
        .search-form { display: flex; gap: 10px; justify-content: center; max-width: 500px; margin: 0 auto; }
        .search-input { flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .search-btn { padding: 10px 25px; background: #66bb6a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .search-btn:hover { background: #2e7d32; }
        .section { padding: 50px 40px; }
        .section h2 { color: #2e7d32; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #66bb6a; padding-bottom: 15px; }
        .cards { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 20px; width: 250px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #66bb6a; }
        .card h3 { color: #2e7d32; margin-bottom: 10px; }
        .card p { color: #666; font-size: 14px; margin-bottom: 15px; }
        .card a { display: inline-block; padding: 8px 15px; background: #66bb6a; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .card a:hover { background: #2e7d32; }
        .badge { display: inline-block; padding: 4px 8px; background: #e74c3c; color: white; border-radius: 4px; font-size: 12px; margin-top: 5px; }
        .badge.info { background: #3498db; }
        .pagination { display: flex; gap: 5px; justify-content: center; margin-top: 30px; flex-wrap: wrap; }
        .page-link { padding: 8px 12px; background: white; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #2e7d32; }
        .page-link.active { background: #2e7d32; color: white; }
        .page-link:hover { background: #66bb6a; color: white; }
        .detail-section { padding: 40px; max-width: 800px; margin: 0 auto; }
        .detail-section h1 { color: #2e7d32; margin-bottom: 20px; }
        .back-link { color: #2e7d32; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #66bb6a; }
        .sub-card { background: #f9f9f9; padding: 10px; border-left: 3px solid #a5d6a7; margin-top: 10px; border-radius: 4px; }
        .empty-state { text-align: center; padding: 40px; color: #666; }
        footer { background-color: #2e7d32; color: white; text-align: center; padding: 15px; margin-top: 40px; }
    </style>
</head>
<body>
    <header>
        <div class="logo">⚕️ Gestion Allergies</div>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="traitement_public.php">Traitements</a>
            <a href="associations_public.php">Associations</a>
            <a href="admin.php">Admin</a>
        </nav>
    </header>

    <?php if ($action === 'accueil'): ?>
        <section class="hero">
            <h1>💊 Traitements des Allergies</h1>
            <p>Découvrez les différentes options thérapeutiques disponibles</p>
            <button onclick="window.location.href='traitement_public.php?action=traitements'">Découvrir</button>
        </section>

        <div class="search-section">
            <form method="GET" action="traitement_public.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher un traitement..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Statistiques</h2>
            <div class="cards">
                <div class="card">
                    <h3>Total Traitements</h3>
                    <p style="font-size: 28px; color: #66bb6a; font-weight: bold;"><?php echo $data['total'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Antihistaminiques</h3>
                    <p style="font-size: 28px; color: #3498db; font-weight: bold;"><?php echo count(array_filter($data['traitements'] ?? [], fn($t) => ($t['type_traitement'] ?? $t['type'] ?? '') === 'antihistaminique')); ?></p>
                </div>
                <div class="card">
                    <h3>Cas d'Urgence</h3>
                    <p style="font-size: 28px; color: #e74c3c; font-weight: bold;">0</p>
                </div>
            </div>
        </section>

        <?php 
        $urgences = [];
        if (!empty($urgences)):
        ?>
            <section class="section">
                <h2>Traitements d'Urgence 🚨</h2>
                <div class="cards">
                    <?php foreach (array_slice($urgences, 0, 3) as $traitement): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($traitement['nom']); ?></h3>
                            <span class="badge">URGENCE</span>
                            <p><?php echo htmlspecialchars(substr($traitement['description'], 0, 80)); ?>...</p>
                            <a href="traitement_public.php?action=detail&id=<?php echo $traitement['id_traitement']; ?>">Détails →</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    <?php elseif ($action === 'traitements'): ?>
        <div class="search-section">
            <form method="GET" action="traitement_public.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher un traitement..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Tous les traitements (<?php echo $data['total']; ?>)</h2>
            <div class="cards">
                <?php if (!empty($data['traitements'])): ?>
                    <?php foreach ($data['traitements'] as $traitement): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($traitement['nom']); ?></h3>
                            <span class="badge info"><?php echo htmlspecialchars(ucfirst($traitement['type_traitement'] ?? '')); ?></span>
                            <p><?php echo htmlspecialchars(substr($traitement['effets_secondaires'] ?? '', 0, 100)); ?></p>
                            <a href="traitement_public.php?action=detail&id=<?php echo $traitement['id_traitement']; ?>">Détails →</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="width: 100%;">Aucun traitement disponible</div>
                <?php endif; ?>
            </div>

            <?php if (($data['nombre_pages'] ?? 1) > 1): ?>
                <div class="pagination">
                    <?php if ($data['page'] > 1): ?>
                        <a href="traitement_public.php?action=traitements&page=1" class="page-link">«</a>
                        <a href="traitement_public.php?action=traitements&page=<?php echo $data['page'] - 1; ?>" class="page-link">‹</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['nombre_pages'], $data['page'] + 2); $i++): ?>
                        <a href="traitement_public.php?action=traitements&page=<?php echo $i; ?>" class="page-link <?php echo $i === $data['page'] ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($data['page'] < $data['nombre_pages']): ?>
                        <a href="traitement_public.php?action=traitements&page=<?php echo $data['page'] + 1; ?>" class="page-link">›</a>
                        <a href="traitement_public.php?action=traitements&page=<?php echo $data['nombre_pages']; ?>" class="page-link">»</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

    <?php elseif ($action === 'detail' && isset($data['traitement'])): ?>
        <div class="detail-section">
            <a href="traitement_public.php?action=traitements" class="back-link">← Retour</a>
            <h1><?php echo htmlspecialchars($data['traitement']['nom']); ?></h1>

            <div class="info-box">
                <h3 style="color: #2e7d32; margin-bottom: 15px;">Informations Générales</h3>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($data['traitement']['type_traitement'] ?? ''); ?></p>
                <p style="margin-top: 10px;"><strong>Posologie:</strong> <?php echo htmlspecialchars($data['traitement']['dosage'] ?? ''); ?></p>
            </div>

            <div class="info-box">
                <h3 style="color: #2e7d32; margin-bottom: 15px;">Description</h3>
                <p><?php echo htmlspecialchars($data['traitement']['effets_secondaires'] ?? ''); ?></p>
            </div>

            <?php if (!empty($data['allergies'])): ?>
                <div class="info-box">
                    <h3 style="color: #2e7d32; margin-bottom: 15px;">Allergies traitées (<?php echo count($data['allergies']); ?>)</h3>
                    <?php foreach ($data['allergies'] as $allergie): ?>
                        <div class="sub-card">
                            <strong><?php echo htmlspecialchars($allergie['nom']); ?></strong>
                            <p style="color: #666; font-size: 12px; margin-top: 5px;">Type: <?php echo htmlspecialchars($allergie['type']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($action === 'rechercher'): ?>
        <div class="search-section">
            <form method="GET" action="traitement_public.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher un traitement..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Résultats pour "<?php echo htmlspecialchars($data['terme'] ?? ''); ?>"</h2>
            <div class="cards">
                <?php if (($data['nombre_resultats'] ?? 0) > 0): ?>
                    <?php foreach ($data['traitements'] as $traitement): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($traitement['nom']); ?></h3>
                            <span class="badge info"><?php echo htmlspecialchars(ucfirst($traitement['type_traitement'] ?? '')); ?></span>
                            <p><?php echo htmlspecialchars(substr($traitement['effets_secondaires'] ?? '', 0, 100)); ?></p>
                            <a href="traitement_public.php?action=detail&id=<?php echo $traitement['id_traitement']; ?>">Détails →</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="width: 100%;">Aucun résultat trouvé</div>
                <?php endif; ?>
            </div>
        </section>

    <?php endif; ?>

    <footer>
        <p>© 2026 Gestion des Allergies et Traitements - Système d'information médical</p>
    </footer>
</body>
</html>
