<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$pdo = config::getConnexion();
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limite = 12;
$offset = ($page - 1) * $limite;

$message = '';
$data = [];

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produit");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_produits = $result['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM categorie");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_categories = $result['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total FROM produit 
    WHERE DATE(date_expiration) <= DATE_ADD(NOW(), INTERVAL 7 DAY)
    AND DATE(date_expiration) > NOW()
");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$produits_expiration = $result['total'];

// Load data
if ($action === 'liste') {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom_cat 
        FROM produit p
        LEFT JOIN categorie c ON p.id_cat = c.id_cat
        ORDER BY p.nom_prod ASC 
        LIMIT " . intval($limite) . " OFFSET " . intval($offset) . "
    ");
    $stmt->execute();
    $data['produits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produit");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $result['total'];
    $nombre_pages = ceil($total / $limite);
} elseif ($action === 'detail') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("
        SELECT p.*, c.nom_cat 
        FROM produit p
        LEFT JOIN categorie c ON p.id_cat = c.id_cat
        WHERE p.id_prod = ?
    ");
    $stmt->execute([$id]);
    $data['produit'] = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - ECOSAVE</title>
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

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 30px;
            border-bottom: 2px solid #66bb6a;
            padding-bottom: 10px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #66bb6a;
            min-width: 150px;
        }

        .stat-card.warning {
            border-left-color: #f39c12;
        }

        .stat-card h3 {
            color: #2e7d32;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #66bb6a;
        }

        .stat-card.warning p {
            color: #f39c12;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #66bb6a 0%, #a5d6a7 100%);
            color: white;
            padding: 20px;
            font-weight: bold;
            font-size: 18px;
        }

        .card-body {
            padding: 20px;
        }

        .card-body p {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .card-footer {
            padding: 0 20px 20px 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: 0.3s;
        }

        .btn-primary {
            background: #2e7d32;
            color: white;
        }

        .btn-primary:hover {
            background: #1e5a23;
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #2e7d32;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 5px;
            background: white;
            color: #2e7d32;
            text-decoration: none;
            border: 1px solid #ddd;
        }

        .pagination a:hover {
            background: #66bb6a;
            color: white;
        }

        .pagination .active {
            background: #2e7d32;
            color: white;
            border-color: #2e7d32;
        }

        .status-expired {
            color: red;
            font-weight: bold;
        }

        .status-warning {
            color: #f39c12;
            font-weight: bold;
        }

        .detail-view {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .detail-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .detail-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .detail-item strong {
            color: #2e7d32;
            display: block;
            margin-bottom: 5px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #66bb6a;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.info {
            background: #3498db;
        }

        footer {
            background: #2e7d32;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        .back-link {
            margin-bottom: 20px;
        }

        .back-link a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <button onclick="window.location.href='index.php'" style="background: none; border: none; color: #2e7d32; font-size: 24px; font-weight: bold; cursor: pointer; padding: 0;">
                🌱 ECOSAVE
            </button>
            <nav>
                <a href="index.php">Allergies</a>
                <a href="traitement_public.php">Traitements</a>
                <a href="associations_public.php">Associations</a>
                <a href="categorie_public.php">📦 Catégories</a>
                <a href="produit_public.php" style="color: #66bb6a;">📊 Produits</a>
                <button onclick="window.location.href='admin.php'" style="background: #4CAF50; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 10px;">
                    🔧 Back Office
                </button>
            </nav>
        </div>
    </header>

    <div class="hero">
        <h1>📊 Nos Produits en Stock</h1>
        <p>Consultez notre inventaire complet et suivi d'expiration</p>
    </div>

    <div class="container">
        <?php if ($action === 'liste'): ?>
            <div class="stats">
                <div class="stat-card">
                    <h3>Total Produits</h3>
                    <p><?php echo $total_produits; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Catégories</h3>
                    <p><?php echo $total_categories; ?></p>
                </div>
                <div class="stat-card warning">
                    <h3>⚠️ À expirer</h3>
                    <p><?php echo $produits_expiration; ?></p>
                </div>
            </div>

            <h2>Tous les Produits</h2>

            <?php if (!empty($data['produits'])): ?>
                <div class="cards-grid">
                    <?php foreach ($data['produits'] as $prod): ?>
                        <?php 
                            $expiration = strtotime($prod['date_expiration']);
                            $now = time();
                            $jours_restants = floor(($expiration - $now) / 86400);
                        ?>
                        <div class="card">
                            <div class="card-header">
                                📦 <?php echo htmlspecialchars($prod['nom_prod']); ?>
                            </div>
                            <div class="card-body">
                                <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($prod['nom_cat'] ?? 'N/A'); ?></p>
                                <p><strong>Quantité :</strong> <?php echo $prod['quantite_dispo']; ?> unités</p>
                                <p><strong>Poids :</strong> <?php echo htmlspecialchars($prod['poids_produit']); ?> kg</p>
                                <p><strong>Expiration :</strong> 
                                    <?php 
                                        if ($jours_restants < 0) {
                                            echo '<span class="status-expired">EXPIRÉ</span>';
                                        } elseif ($jours_restants <= 7) {
                                            echo '<span class="status-warning">' . $jours_restants . ' j</span>';
                                        } else {
                                            echo date('d/m/Y', $expiration);
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="produit_public.php?action=detail&id=<?php echo $prod['id_prod']; ?>" class="btn btn-primary">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($nombre_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($p = 1; $p <= $nombre_pages; $p++): ?>
                            <?php if ($p === $page): ?>
                                <span class="active"><?php echo $p; ?></span>
                            <?php else: ?>
                                <a href="produit_public.php?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align: center; color: #999;">Aucun produit trouvé</p>
            <?php endif; ?>

        <?php elseif ($action === 'detail' && isset($data['produit'])): ?>
            <div class="back-link">
                <a href="produit_public.php">← Retour aux produits</a>
            </div>

            <div class="detail-view">
                <div class="detail-content">
                    <div class="detail-info">
                        <h2 style="border: none;">📦 <?php echo htmlspecialchars($data['produit']['nom_prod']); ?></h2>
                        
                        <div class="detail-item">
                            <strong>Catégorie</strong>
                            <span class="badge info"><?php echo htmlspecialchars($data['produit']['nom_cat'] ?? 'N/A'); ?></span>
                        </div>

                        <div class="detail-item">
                            <strong>Quantité Disponible</strong>
                            <?php echo $data['produit']['quantite_dispo']; ?> unités
                        </div>

                        <div class="detail-item">
                            <strong>Poids</strong>
                            <?php echo htmlspecialchars($data['produit']['poids_produit']); ?> kg
                        </div>

                        <div class="detail-item">
                            <strong>Date d'Expiration</strong>
                            <?php 
                                $expiration = strtotime($data['produit']['date_expiration']);
                                $now = time();
                                $jours_restants = floor(($expiration - $now) / 86400);
                            ?>
                            <?php if ($jours_restants < 0): ?>
                                <span class="status-expired">EXPIRÉ depuis <?php echo abs($jours_restants); ?> j</span>
                            <?php elseif ($jours_restants <= 7): ?>
                                <span class="status-warning">Expire dans <?php echo $jours_restants; ?> jours</span>
                            <?php else: ?>
                                <?php echo date('d/m/Y', $expiration); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="background: linear-gradient(135deg, #66bb6a 0%, #a5d6a7 100%); border-radius: 10px; padding: 30px; color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                        <p style="font-size: 48px; margin-bottom: 15px;">📦</p>
                        <p style="font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($data['produit']['nom_prod']); ?></p>
                        <p style="margin-top: 15px; opacity: 0.95;">Produit en gestion de stock</p>
                        <p style="margin-top: 10px; font-size: 24px; font-weight: bold;">
                            <?php echo $data['produit']['quantite_dispo']; ?> unités
                        </p>
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 10px;">
                    <h3 style="color: #2e7d32; margin-bottom: 15px;">Informations</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <p><strong>Créé le :</strong> <?php echo date('d/m/Y H:i', strtotime($data['produit']['date_creation'])); ?></p>
                        </div>
                        <div>
                            <p><strong>Modifié le :</strong> <?php echo date('d/m/Y H:i', strtotime($data['produit']['date_modification'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="back-link">
                <a href="produit_public.php">← Retour aux produits</a>
            </div>
            <p style="text-align: center; color: #999;">Produit non trouvé</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 ECOSAVE - Gestion Intelligente des Allergies et Produits</p>
    </footer>
</body>
</html>
