<?php
session_start();
require_once __DIR__ . '/../models/Allergie.php';
require_once __DIR__ . '/../controllers/AllergiController.php';

$controller = new AllergiController();
$action = isset($_GET['action']) ? $_GET['action'] : 'accueil';
$data = [];

switch ($action) {
    case 'detail':
        $id = intval($_GET['id'] ?? 0);
        $data = $controller->afficherDetailAllergie($id);
        if (isset($data['erreur'])) $action = 'accueil';
        break;
    case 'rechercher':
        $terme = $_GET['q'] ?? '';
        $data = strlen(trim($terme)) > 0 ? $controller->rechercherAllergies($terme) : $controller->afficherListePublique();
        break;
    case 'allergies':
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
    <title>Allergies - Managedical</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f9f4; }

        header {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }

        nav a:hover {
            background: #f0f0f0;
        }
        .hero {
            background: linear-gradient(135deg, #66bb6a 0%, #a5d6a7 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            opacity: 0.95;
        }
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
        .pagination { display: flex; gap: 5px; justify-content: center; margin-top: 30px; flex-wrap: wrap; }
        .page-link { padding: 8px 12px; background: white; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #2e7d32; }
        .page-link.active { background: #2e7d32; color: white; }
        .page-link:hover { background: #66bb6a; color: white; }
        .detail-section { padding: 40px; max-width: 800px; margin: 0 auto; }
        .detail-section h1 { color: #2e7d32; margin-bottom: 20px; }
        .back-link { color: #2e7d32; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #66bb6a; }
        .empty-state { text-align: center; padding: 40px; color: #666; }
        footer { background-color: #2e7d32; color: white; text-align: center; padding: 15px; margin-top: 40px; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <button onclick="window.location.href='index.php'" style="background: none; border: none; color: #2e7d32; font-size: 24px; font-weight: bold; cursor: pointer; padding: 0;">
                🌱 ECOSAVE
            </button>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="traitement_public.php">Traitements</a>
                <a href="associations_public.php">Associations</a>
                <a href="categorie_public.php">📦 Catégories</a>
                <a href="produit_public.php">📊 Produits</a>
                <button onclick="window.location.href='admin.php'" style="background: #4CAF50; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 10px;">
                    🔧 Back Office
                </button>
            </nav>
        </div>
    </header>

    <?php if ($action === 'accueil'): ?>
        <section class="hero">
            <h1>⚠️ Gestion des Allergies</h1>
            <p>Informations complètes sur les allergies et leurs traitements</p>
            <button onclick="window.location.href='index.php?action=allergies'">Découvrir</button>
        </section>

        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher une allergie..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Statistiques</h2>
            <div class="cards">
                <div class="card">
                    <h3>Total Allergies</h3>
                    <p style="font-size: 28px; color: #66bb6a; font-weight: bold;"><?php echo $data['total'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Critiques</h3>
                    <p style="font-size: 28px; color: #e74c3c; font-weight: bold;"><?php echo count(array_filter($data['allergies'] ?? [], fn($a) => $a['niveau_danger'] === 'critique')); ?></p>
                </div>
                <div class="card">
                    <h3>Alimentaires</h3>
                    <p style="font-size: 28px; color: #66bb6a; font-weight: bold;"><?php echo count(array_filter($data['allergies'] ?? [], fn($a) => $a['type'] === 'alimentaire')); ?></p>
                </div>
            </div>
        </section>

        <?php 
        $critiques = array_filter($data['allergies'] ?? [], fn($a) => $a['niveau_danger'] === 'critique');
        if (!empty($critiques)):
        ?>
            <section class="section">
                <h2>Allergies Critiques ⚠️</h2>
                <div class="cards">
                    <?php foreach (array_slice($critiques, 0, 3) as $allergie): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($allergie['nom']); ?></h3>
                            <span class="badge">CRITIQUE</span>
                            <p><?php echo htmlspecialchars(substr($allergie['description'], 0, 80)); ?>...</p>
                            <a href="index.php?action=detail&id=<?php echo $allergie['id_allergie']; ?>">Détails →</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    <?php elseif ($action === 'allergies'): ?>
        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher une allergie..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Toutes les allergies (<?php echo $data['total']; ?>)</h2>
            <div class="cards">
                <?php if (!empty($data['allergies'])): ?>
                    <?php foreach ($data['allergies'] as $allergie): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($allergie['nom']); ?></h3>
                            <span class="badge"><?php echo htmlspecialchars(ucfirst($allergie['niveau_danger'])); ?></span>
                            <p><?php echo htmlspecialchars(substr($allergie['description'], 0, 100)); ?></p>
                            <a href="index.php?action=detail&id=<?php echo $allergie['id_allergie']; ?>">Détails →</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="width: 100%;">Aucune allergie disponible</div>
                <?php endif; ?>
            </div>

            <?php if (($data['nombre_pages'] ?? 1) > 1): ?>
                <div class="pagination">
                    <?php if ($data['page'] > 1): ?>
                        <a href="index.php?action=allergies&page=1" class="page-link">«</a>
                        <a href="index.php?action=allergies&page=<?php echo $data['page'] - 1; ?>" class="page-link">‹</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['nombre_pages'], $data['page'] + 2); $i++): ?>
                        <a href="index.php?action=allergies&page=<?php echo $i; ?>" class="page-link <?php echo $i === $data['page'] ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($data['page'] < $data['nombre_pages']): ?>
                        <a href="index.php?action=allergies&page=<?php echo $data['page'] + 1; ?>" class="page-link">›</a>
                        <a href="index.php?action=allergies&page=<?php echo $data['nombre_pages']; ?>" class="page-link">»</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

    <?php elseif ($action === 'detail' && isset($data['allergie'])): ?>
        <div class="detail-section">
            <a href="index.php?action=allergies" class="back-link">← Retour</a>
            <h1><?php echo htmlspecialchars($data['allergie']['nom']); ?></h1>

            <div class="info-box">
                <h3 style="color: #2e7d32; margin-bottom: 15px;">Informations Générales</h3>
                <p><strong>Niveau de danger:</strong> <span class="badge"><?php echo htmlspecialchars(ucfirst($data['allergie']['niveau_danger'])); ?></span></p>
                <p style="margin-top: 10px;"><strong>Type:</strong> <?php echo htmlspecialchars($data['allergie']['type']); ?></p>
                <p style="margin-top: 10px;"><strong>Description:</strong> <?php echo htmlspecialchars($data['allergie']['description']); ?></p>
            </div>

            <div class="info-box">
                <h3 style="color: #2e7d32; margin-bottom: 15px;">Symptômes</h3>
                <p><?php echo htmlspecialchars($data['allergie']['symptomes']); ?></p>
            </div>
        </div>

    <?php elseif ($action === 'rechercher'): ?>
        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="action" value="rechercher">
                <input type="text" name="q" placeholder="Rechercher une allergie..." class="search-input" required>
                <button type="submit" class="search-btn">🔍 Rechercher</button>
            </form>
        </div>

        <section class="section">
            <h2>Résultats pour "<?php echo htmlspecialchars($data['terme'] ?? ''); ?>"</h2>
            <div class="cards">
                <?php if (($data['nombre_resultats'] ?? 0) > 0): ?>
                    <?php foreach ($data['allergies'] as $allergie): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($allergie['nom']); ?></h3>
                            <span class="badge"><?php echo htmlspecialchars(ucfirst($allergie['niveau_danger'])); ?></span>
                            <p><?php echo htmlspecialchars(substr($allergie['description'], 0, 100)); ?></p>
                            <a href="index.php?action=detail&id=<?php echo $allergie['id_allergie']; ?>">Détails →</a>
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
