<?php
session_start();
require_once __DIR__ . '/app/models/Produit.php';
require_once __DIR__ . '/app/controllers/ProduitController.php';
require_once __DIR__ . '/config/Database.php';

$controller = new ProduitController();
$pdo = Database::getInstance()->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$message = '';
$erreurs = [];
$data = [];

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'creer':
            $result = $controller->creerProduit($_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'];
                $action = 'ajouter';
            }
            break;

        case 'modifier':
            $id = intval($_POST['id_prod'] ?? 0);
            $result = $controller->mettreAJourProduit($id, $_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'] ?? [];
                $action = 'editer';
            }
            break;

        case 'supprimer':
            $id = intval($_POST['id_prod'] ?? 0);
            $result = $controller->supprimerProduit($id);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
            } else {
                $erreurs = ['erreur' => $result['erreur'] ?? 'Erreur lors de la suppression'];
            }
            $action = 'liste';
            break;
    }
}

// Get statistics
$stmt_prod = $pdo->prepare("SELECT COUNT(*) as total FROM produit");
$stmt_prod->execute();
$result = $stmt_prod->fetch(PDO::FETCH_ASSOC);
$total_produits = $result['total'];

$stmt_cat = $pdo->prepare("SELECT COUNT(*) as total FROM categorie");
$stmt_cat->execute();
$result = $stmt_cat->fetch(PDO::FETCH_ASSOC);
$total_categories = $result['total'];

$stmt_exp = $pdo->prepare("
    SELECT COUNT(*) as total FROM produit 
    WHERE DATE(date_expiration) <= DATE_ADD(NOW(), INTERVAL 7 DAY)
    AND DATE(date_expiration) > NOW()
");
$stmt_exp->execute();
$result = $stmt_exp->fetch(PDO::FETCH_ASSOC);
$produits_expiration = $result['total'];

// Load data based on action
if ($action === 'liste') {
    $data = $controller->afficherListeAdmin();
} elseif ($action === 'ajouter') {
    $data = $controller->afficherFormulaireAjout();
} elseif ($action === 'editer') {
    $id = intval($_GET['id'] ?? 0);
    $data = $controller->afficherFormulaireEdition($id);
    if (isset($data['erreur'])) {
        $erreurs[] = $data['erreur'];
        $action = 'liste';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Produits</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; }

        .sidebar {
            width: 240px;
            background: #2e7d32;
            color: white;
            height: 100vh;
            padding: 20px;
            overflow-y: auto;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 18px;
            word-wrap: break-word;
            white-space: normal;
            overflow: visible;
            color: white;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 12px 0;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }

        .main {
            flex: 1;
            margin-left: 240px;
            padding: 20px;
            background: #f4f9f4;
            min-height: 100vh;
        }

        h1 { color: #2e7d32; margin-bottom: 20px; }
        h2 { color: #2e7d32; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #66bb6a; padding-bottom: 10px; }

        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert ul { margin: 5px 0 0 20px; }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #66bb6a;
        }

        .card.warning {
            border-left-color: #f39c12;
        }

        .card h3 { color: #2e7d32; margin-bottom: 10px; font-size: 14px; }
        .card p { font-size: 32px; font-weight: bold; color: #66bb6a; }
        .card.warning p { color: #f39c12; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th {
            background: #2e7d32;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover { background: #f9f9f9; }

        .status-expiration {
            color: #e74c3c;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-icon {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
        }

        .btn-icon:hover { opacity: 0.7; }
        .btn-delete { color: red; }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2e7d32;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2e7d32;
            color: white;
        }

        .btn-primary:hover {
            background: #1e5a23;
        }

        .btn-secondary {
            background: #999;
            color: white;
        }

        .btn-secondary:hover {
            background: #777;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .nav-section {
            margin-top: 30px;
        }

        .nav-section-title {
            font-size: 12px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.6);
            margin: 15px 0 10px 0;
            font-weight: bold;
        }

        hr {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🌱 ECOSAVE</h2>
        
        <div class="nav-section">
            <div class="nav-section-title">Allergies</div>
            <a href="admin.php">📋 Liste</a>
            <a href="admin.php?action=ajouter">➕ Ajouter</a>
        </div>

        <hr>

        <div class="nav-section">
            <div class="nav-section-title">Traitements</div>
            <a href="traitement.php">💊 Liste</a>
            <a href="traitement.php?action=ajouter">➕ Ajouter</a>
        </div>

        <hr>

        <div class="nav-section">
            <div class="nav-section-title">Associations</div>
            <a href="associations.php">🔗 Liste</a>
            <a href="associations.php?action=ajouter">➕ Ajouter</a>
        </div>

        <hr>

        <div class="nav-section">
            <div class="nav-section-title">Produits</div>
            <a href="categorie.php">📦 Catégories</a>
            <a href="categorie.php?action=ajouter">➕ Ajouter</a>
        </div>

        <hr>

        <div class="nav-section">
            <div class="nav-section-title">Gestion Stock</div>
            <a href="produit.php">📊 Produits</a>
            <a href="produit.php?action=ajouter">➕ Ajouter</a>
        </div>
    </div>

    <div class="main">
        <h1>📊 <?php echo ['liste' => 'Liste des produits', 'ajouter' => 'Ajouter un produit', 'editer' => 'Éditer un produit'][$action] ?? 'Produits'; ?></h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-error">
                <strong>Erreurs :</strong>
                <ul>
                    <?php foreach ($erreurs as $err): ?>
                        <li><?php echo is_array($err) ? implode(', ', (array)$err) : htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($action === 'liste'): ?>
            <div class="cards">
                <div class="card">
                    <h3>Produits</h3>
                    <p><?php echo $total_produits; ?></p>
                </div>
                <div class="card">
                    <h3>Catégories</h3>
                    <p><?php echo $total_categories; ?></p>
                </div>
                <div class="card warning">
                    <h3>⚠️ À expirer</h3>
                    <p><?php echo $produits_expiration; ?></p>
                </div>
            </div>

            <div class="section-header">
                <h2>Produits (<?php echo count($data['produits'] ?? []); ?>)</h2>
                <a href="produit.php?action=ajouter" class="btn btn-primary">➕ Ajouter</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Poids</th>
                        <th>Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['produits'])): ?>
                        <?php foreach ($data['produits'] as $prod): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($prod['id_prod']); ?></td>
                                <td><strong><?php echo htmlspecialchars($prod['nom_prod']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['nom_cat'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($prod['quantite_dispo']); ?></td>
                                <td><?php echo htmlspecialchars($prod['poids_produit']); ?> kg</td>
                                <td>
                                    <?php 
                                        $expiration = strtotime($prod['date_expiration']);
                                        $now = time();
                                        $jours_restants = floor(($expiration - $now) / 86400);
                                        if ($jours_restants < 0) {
                                            echo '<span style="color: red; font-weight: bold;">EXPIRÉ</span>';
                                        } elseif ($jours_restants <= 7) {
                                            echo '<span class="status-expiration">' . $jours_restants . ' j</span>';
                                        } else {
                                            echo date('d/m/Y', $expiration);
                                        }
                                    ?>
                                </td>
                                <td class="action-buttons">
                                    <a href="produit.php?action=editer&id=<?php echo $prod['id_prod']; ?>" class="btn-icon" title="Éditer">✏️</a>
                                    <form method="POST" action="produit.php?action=supprimer" style="display: inline;">
                                        <input type="hidden" name="id_prod" value="<?php echo $prod['id_prod']; ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999;">Aucun produit trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'ajouter' || $action === 'editer'): ?>
            <form method="POST" action="produit.php?action=<?php echo $action === 'editer' ? 'modifier' : 'creer'; ?>" class="form-container">
                
                <?php if ($action === 'editer' && isset($data['produit'])): ?>
                    <input type="hidden" name="id_prod" value="<?php echo htmlspecialchars($data['produit']['id_prod']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nom_prod">Nom du produit *</label>
                    <input type="text" id="nom_prod" name="nom_prod" value="<?php echo htmlspecialchars($data['produit']['nom_prod'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="id_cat">Catégorie *</label>
                        <select id="id_cat" name="id_cat" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($data['categories'] ?? [] as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['id_cat']); ?>"
                                        <?php echo (isset($data['produit']['id_cat']) && $data['produit']['id_cat'] == $cat['id_cat']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nom_cat']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_expiration">Date d'expiration *</label>
                        <input type="date" id="date_expiration" name="date_expiration" value="<?php echo htmlspecialchars($data['produit']['date_expiration'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="poids_produit">Poids (kg) *</label>
                        <input type="number" id="poids_produit" name="poids_produit" value="<?php echo htmlspecialchars($data['produit']['poids_produit'] ?? ''); ?>" required step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="quantite_dispo">Quantité disponible *</label>
                        <input type="number" id="quantite_dispo" name="quantite_dispo" value="<?php echo htmlspecialchars($data['produit']['quantite_dispo'] ?? ''); ?>" required min="0">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="produit.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $action === 'editer' ? '✓ Modifier' : '✓ Ajouter'; ?>
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
