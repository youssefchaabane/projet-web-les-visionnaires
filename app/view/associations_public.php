<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$pdo = config::getConnexion();
$action = isset($_GET['action']) ? $_GET['action'] : 'accueil';
$data = [];

if ($action === 'by_allergie') {
    $id_allergie = intval($_GET['id'] ?? 0);
    if ($id_allergie > 0) {
        $stmt = $pdo->prepare("
            SELECT ta.*, a.nom as allergie_nom, t.nom as traitement_nom
            FROM allergie_traitement ta
            JOIN allergie a ON ta.id_allergie = a.id_allergie
            JOIN traitement t ON ta.id_traitement = t.id_traitement
            WHERE ta.id_allergie = ?
        ");
        $stmt->execute([$id_allergie]);
        $data['associations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['filter_type'] = 'allergie';
        $data['filter_id'] = $id_allergie;
        $action = 'filtered';
    }
} elseif ($action === 'by_traitement') {
    $id_traitement = intval($_GET['id'] ?? 0);
    if ($id_traitement > 0) {
        $stmt = $pdo->prepare("
            SELECT ta.*, a.nom as allergie_nom, t.nom as traitement_nom
            FROM allergie_traitement ta
            JOIN allergie a ON ta.id_allergie = a.id_allergie
            JOIN traitement t ON ta.id_traitement = t.id_traitement
            WHERE ta.id_traitement = ?
        ");
        $stmt->execute([$id_traitement]);
        $data['associations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['filter_type'] = 'traitement';
        $data['filter_id'] = $id_traitement;
        $action = 'filtered';
    }
} else {
    $items_per_page = 12;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = max(1, $page);
    $offset = ($page - 1) * $items_per_page;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM allergie_traitement");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $data['total'] = $result['total'];
    $data['nombre_pages'] = ceil($data['total'] / $items_per_page);
    $data['page'] = $page;

    $stmt = $pdo->prepare("
        SELECT ta.*, a.nom as allergie_nom, a.niveau_danger, t.nom as traitement_nom
        FROM allergie_traitement ta
        JOIN allergie a ON ta.id_allergie = a.id_allergie
        JOIN traitement t ON ta.id_traitement = t.id_traitement
        ORDER BY a.nom, t.nom
        LIMIT " . intval($items_per_page) . " OFFSET " . intval($offset)
    );
    $stmt->execute();
    $data['associations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $action = 'accueil';
}

if ($action === 'accueil') {
    $stmt_total = $pdo->prepare("SELECT COUNT(*) as total FROM allergie_traitement");
    $stmt_total->execute();
    $total_result = $stmt_total->fetch(PDO::FETCH_ASSOC);
    $data['total_associations'] = $total_result['total'];

    $stmt_allerg = $pdo->prepare("SELECT COUNT(DISTINCT id_allergie) as total FROM allergie_traitement");
    $stmt_allerg->execute();
    $result = $stmt_allerg->fetch(PDO::FETCH_ASSOC);
    $data['total_allergies'] = $result['total'];

    $stmt_trait = $pdo->prepare("SELECT COUNT(DISTINCT id_traitement) as total FROM allergie_traitement");
    $stmt_trait->execute();
    $result = $stmt_trait->fetch(PDO::FETCH_ASSOC);
    $data['total_traitements'] = $result['total'];

    $stmt_crit = $pdo->prepare("
        SELECT COUNT(DISTINCT ta.id) as total
        FROM allergie_traitement ta
        JOIN allergie a ON ta.id_allergie = a.id_allergie
        WHERE a.niveau_danger = 'critique'
    ");
    $stmt_crit->execute();
    $result = $stmt_crit->fetch(PDO::FETCH_ASSOC);
    $data['critical_associations'] = $result['total'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associations - Managedical</title>
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
        .section { padding: 50px 40px; }
        .section h2 { color: #2e7d32; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #66bb6a; padding-bottom: 15px; }
        .cards { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .card { background: white; padding: 20px; width: 250px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #66bb6a; }
        .card h3 { color: #2e7d32; margin-bottom: 10px; font-size: 16px; }
        .card p { color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.5; }
        .card a { display: inline-block; padding: 8px 15px; background: #66bb6a; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .card a:hover { background: #2e7d32; }
        .badge { display: inline-block; padding: 4px 8px; background: #e74c3c; color: white; border-radius: 4px; font-size: 12px; margin-top: 5px; margin-right: 5px; }
        .badge.danger { background: #e74c3c; }
        .badge.info { background: #3498db; }
        .pagination { display: flex; gap: 5px; justify-content: center; margin-top: 30px; flex-wrap: wrap; }
        .page-link { padding: 8px 12px; background: white; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #2e7d32; }
        .page-link.active { background: #2e7d32; color: white; }
        .page-link:hover { background: #66bb6a; color: white; }
        .detail-section { padding: 40px; max-width: 900px; margin: 0 auto; }
        .detail-section h1 { color: #2e7d32; margin-bottom: 20px; }
        .back-link { color: #2e7d32; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .info-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #66bb6a; }
        .empty-state { text-align: center; padding: 40px; color: #666; width: 100%; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th { background: #2e7d32; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
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
            <h1>🔗 Associations Allergie-Traitement</h1>
            <p>Découvrez les relations entre allergies et leurs traitements</p>
            <button onclick="window.location.href='associations_public.php?action=list'">Voir toutes les associations</button>
        </section>

        <section class="section">
            <h2>Statistiques Générales</h2>
            <div class="cards">
                <div class="card">
                    <h3>Associations Totales</h3>
                    <p style="font-size: 28px; color: #66bb6a; font-weight: bold;"><?php echo $data['total_associations'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Allergies Impliquées</h3>
                    <p style="font-size: 28px; color: #3498db; font-weight: bold;"><?php echo $data['total_allergies'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Traitements Impliqués</h3>
                    <p style="font-size: 28px; color: #a5d6a7; font-weight: bold;"><?php echo $data['total_traitements'] ?? 0; ?></p>
                </div>
            </div>
        </section>

        <?php if (($data['critical_associations'] ?? 0) > 0): ?>
            <section class="section">
                <h2>Associations Critiques 🚨</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px;"><?php echo $data['critical_associations']; ?> association(s) impliquant des allergies critiques</p>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Allergie</th>
                                <th>Traitement</th>
                                <th>Risque</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </section>
        <?php endif; ?>

    <?php elseif ($action === 'filtered'): ?>
        <div class="detail-section">
            <a href="associations_public.php" class="back-link">← Retour aux associations</a>
            <h1><?php echo $data['filter_type'] === 'allergie' ? 'Traitements pour cette allergie' : 'Allergies traitées par ce traitement'; ?></h1>

            <?php if (!empty($data['associations'])): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Allergie</th>
                                <th>Traitement</th>
                                <th>Date d'Association</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['associations'] as $assoc): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($assoc['allergie_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($assoc['traitement_nom']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($assoc['date_ajout'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">Aucune association trouvée</div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <?php if ($action === 'accueil' || ($action === 'accueil' && !isset($data['filter_type']))): ?>
        <section class="section">
            <h2>Toutes les Associations (<?php echo $data['total'] ?? 0; ?>)</h2>
            <div class="cards">
                <?php if (!empty($data['associations'])): ?>
                    <?php foreach ($data['associations'] as $assoc): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($assoc['allergie_nom']); ?></h3>
                            <p style="color: #666; font-size: 13px; margin-bottom: 10px;">
                                <strong>Traitement:</strong><br/>
                                <?php echo htmlspecialchars($assoc['traitement_nom']); ?>
                            </p>
                            <?php if ($assoc['niveau_danger'] === 'critique'): ?>
                                <span class="badge danger">CRITIQUE</span>
                            <?php endif; ?>
                            <p style="color: #999; font-size: 12px; margin-top: 10px;">
                                <?php echo date('d/m/Y', strtotime($assoc['date_ajout'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">Aucune association disponible</div>
                <?php endif; ?>
            </div>

            <?php if (($data['nombre_pages'] ?? 1) > 1): ?>
                <div class="pagination">
                    <?php if ($data['page'] > 1): ?>
                        <a href="associations_public.php?page=1" class="page-link">«</a>
                        <a href="associations_public.php?page=<?php echo $data['page'] - 1; ?>" class="page-link">‹</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['nombre_pages'], $data['page'] + 2); $i++): ?>
                        <a href="associations_public.php?page=<?php echo $i; ?>" class="page-link <?php echo $i === $data['page'] ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($data['page'] < $data['nombre_pages']): ?>
                        <a href="associations_public.php?page=<?php echo $data['page'] + 1; ?>" class="page-link">›</a>
                        <a href="associations_public.php?page=<?php echo $data['nombre_pages']; ?>" class="page-link">»</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <footer>
        <p>© 2026 Gestion des Allergies et Traitements - Système d'information médical</p>
    </footer>
</body>
</html>
