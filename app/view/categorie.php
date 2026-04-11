<?php
session_start();
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../controllers/CategoriController.php';
require_once __DIR__ . '/../../config/config.php';

$controller = new CategoriController();
$pdo = config::getConnexion();
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$message = '';
$erreurs = [];
$data = [];

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'creer':
            $result = $controller->creerCategorie($_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'];
                $action = 'ajouter';
            }
            break;

        case 'modifier':
            $id = intval($_POST['id_cat'] ?? 0);
            $result = $controller->mettreAJourCategorie($id, $_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'] ?? [];
                $action = 'editer';
            }
            break;

        case 'supprimer':
            $id = intval($_POST['id_cat'] ?? 0);
            $result = $controller->supprimerCategorie($id);
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
$stmt_cat = $pdo->prepare("SELECT COUNT(*) as total FROM categorie");
$stmt_cat->execute();
$result = $stmt_cat->fetch(PDO::FETCH_ASSOC);
$total_categories = $result['total'];

$stmt_prod = $pdo->prepare("SELECT COUNT(*) as total FROM produit");
$stmt_prod->execute();
$result = $stmt_prod->fetch(PDO::FETCH_ASSOC);
$total_produits = $result['total'];

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
    <title>Admin - Gestion des Catégories</title>
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

        .card h3 { color: #2e7d32; margin-bottom: 10px; font-size: 14px; }
        .card p { font-size: 32px; font-weight: bold; color: #66bb6a; }

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

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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

        .view-link {
            margin-top: 20px;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            text-align: center;
        }

        .view-link a {
            color: white;
            text-decoration: none;
            margin: 0;
        }

        .view-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🌱 ECOSAVE</h2>
        
        <button onclick="window.location.href='../view/index.php'" class="switch-btn" style="background: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; width: 100%; font-weight: bold;">
            🏠 Front Office
        </button>
        
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
        <h1>📦 <?php echo ['liste' => 'Liste des catégories', 'ajouter' => 'Ajouter une catégorie', 'editer' => 'Éditer une catégorie'][$action] ?? 'Catégories'; ?></h1>

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
                    <h3>Catégories</h3>
                    <p><?php echo $total_categories; ?></p>
                </div>
                <div class="card">
                    <h3>Produits</h3>
                    <p><?php echo $total_produits; ?></p>
                </div>
            </div>

            <div class="section-header">
                <h2>Catégories (<?php echo count($data['categories'] ?? []); ?>)</h2>
                <a href="categorie.php?action=ajouter" class="btn btn-primary">➕ Ajouter</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Lieu Stockage</th>
                        <th>Température</th>
                        <th>Délai Alerte</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['categories'])): ?>
                        <?php foreach ($data['categories'] as $cat): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($cat['id_cat']); ?></td>
                                <td><strong><?php echo htmlspecialchars($cat['nom_cat']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cat['lieu_stockage']); ?></td>
                                <td><?php echo htmlspecialchars($cat['temp_conseille']); ?>°C</td>
                                <td><?php echo htmlspecialchars($cat['delai_alerte_jours']); ?> j</td>
                                <td class="action-buttons">
                                    <a href="categorie.php?action=editer&id=<?php echo $cat['id_cat']; ?>" class="btn-icon" title="Éditer">✏️</a>
                                    <form method="POST" action="categorie.php?action=supprimer" style="display: inline;">
                                        <input type="hidden" name="id_cat" value="<?php echo $cat['id_cat']; ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #999;">Aucune catégorie trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'ajouter' || $action === 'editer'): ?>
            <form method="POST" action="categorie.php?action=<?php echo $action === 'editer' ? 'modifier' : 'creer'; ?>" class="form-container">
                
                <?php if ($action === 'editer' && isset($data['categorie'])): ?>
                    <input type="hidden" name="id_cat" value="<?php echo htmlspecialchars($data['categorie']['id_cat']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nom_cat">Nom de la catégorie *</label>
                    <input type="text" id="nom_cat" name="nom_cat" value="<?php echo htmlspecialchars($data['categorie']['nom_cat'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="description_cat">Description *</label>
                    <textarea id="description_cat" name="description_cat" required><?php echo htmlspecialchars($data['categorie']['description_cat'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lieu_stockage">Lieu de stockage *</label>
                        <select id="lieu_stockage" name="lieu_stockage" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($data['lieux'] ?? [] as $lieu): ?>
                                <option value="<?php echo htmlspecialchars($lieu); ?>" 
                                        <?php echo (isset($data['categorie']['lieu_stockage']) && $data['categorie']['lieu_stockage'] === $lieu) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lieu); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="temp_conseille">Température (°C)</label>
                        <input type="number" id="temp_conseille" name="temp_conseille" value="<?php echo htmlspecialchars($data['categorie']['temp_conseille'] ?? ''); ?>" step="0.1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="delai_alerte_jours">Délai d'alerte (jours)</label>
                    <input type="number" id="delai_alerte_jours" name="delai_alerte_jours" value="<?php echo htmlspecialchars($data['categorie']['delai_alerte_jours'] ?? '7'); ?>" min="1">
                </div>

                <div class="form-actions">
                    <a href="categorie.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $action === 'editer' ? '✓ Modifier' : '✓ Ajouter'; ?>
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
