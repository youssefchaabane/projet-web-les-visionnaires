<?php
session_start();
require_once __DIR__ . '/../models/Traitement.php';
require_once __DIR__ . '/../controllers/TraitementController.php';
require_once __DIR__ . '/../../config/config.php';

$controller = new TraitementController();
$pdo = config::getConnexion();
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$message = '';
$erreurs = [];
$data = [];

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'creer':
            $result = $controller->creerTraitement($_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'];
                $action = 'ajouter';
            }
            break;

        case 'modifier':
            $id = intval($_POST['id_traitement'] ?? 0);
            $result = $controller->mettreAJourTraitement($id, $_POST);
            if ($result['succes']) {
                $message = "✓ " . $result['message'];
                $action = 'liste';
            } else {
                $erreurs = $result['erreurs'] ?? [];
                $action = 'editer';
            }
            break;

        case 'supprimer':
            $id = intval($_POST['id_traitement'] ?? 0);
            $result = $controller->supprimerTraitement($id);
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
$stmt_trait = $pdo->prepare("SELECT COUNT(*) as total FROM traitement");
$stmt_trait->execute();
$result = $stmt_trait->fetch(PDO::FETCH_ASSOC);
$total_traitements = $result['total'];

$stmt_allerg = $pdo->prepare("SELECT COUNT(*) as total FROM allergie");
$stmt_allerg->execute();
$result = $stmt_allerg->fetch(PDO::FETCH_ASSOC);
$total_allergies = $result['total'];

$stmt_assoc = $pdo->prepare("SELECT COUNT(*) as total FROM allergie_traitement");
$stmt_assoc->execute();
$result = $stmt_assoc->fetch(PDO::FETCH_ASSOC);
$total_associations = $result['total'];

$stmt_antihist = $pdo->prepare("SELECT COUNT(*) as total FROM traitement WHERE type_traitement = 'antihistaminique'");
$stmt_antihist->execute();
$result = $stmt_antihist->fetch(PDO::FETCH_ASSOC);
$antihistaminique_count = $result['total'];

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
    <title>Admin - Gestion des Traitements</title>
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

        .alert ul {
            margin: 5px 0 0 20px;
        }

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

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-antihistaminique { background: #3498db; color: white; }
        .badge-corticosteroids { background: #e74c3c; color: white; }
        .badge-epinephrine { background: #c0392b; color: white; }
        .badge-decongestant { background: #27ae60; color: white; }
        .badge-antileukotriene { background: #f39c12; color: white; }

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

        .btn-icon:hover {
            opacity: 0.7;
        }

        .btn-delete {
            color: red;
        }

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

        <div class="view-link">
            <a href="traitement_public.php">👁️ Voir FrontOffice</a>
        </div>
    </div>

    <div class="main">
        <h1>💊 <?php echo ['liste' => 'Liste des traitements', 'ajouter' => 'Ajouter un traitement', 'editer' => 'Éditer un traitement'][$action] ?? 'Traitements'; ?></h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
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
                    <h3>Traitements Totaux</h3>
                    <p><?php echo $total_traitements; ?></p>
                </div>

                <div class="card">
                    <h3>Antihistaminiques</h3>
                    <p><?php echo $antihistaminique_count; ?></p>
                </div>

                <div class="card">
                    <h3>Allergies</h3>
                    <p><?php echo $total_allergies; ?></p>
                </div>

                <div class="card">
                    <h3>Associations</h3>
                    <p><?php echo $total_associations; ?></p>
                </div>
            </div>

            <div class="section-header">
                <h2>Traitements (<?php echo count($data['traitements'] ?? []); ?>)</h2>
                <a href="traitement.php?action=ajouter" class="btn btn-primary">➕ Ajouter</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Dosage</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['traitements'])): ?>
                        <?php foreach ($data['traitements'] as $traitement): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($traitement['id_traitement']); ?></td>
                                <td><strong><?php echo htmlspecialchars($traitement['nom']); ?></strong></td>
                                <td><span class="badge badge-<?php echo strtolower($traitement['type_traitement']); ?>"><?php echo htmlspecialchars(ucfirst($traitement['type_traitement'])); ?></span></td>
                                <td><?php echo htmlspecialchars($traitement['dosage']); ?></td>
                                <td><?php echo htmlspecialchars($traitement['duree']); ?></td>
                                <td class="action-buttons">
                                    <a href="traitement.php?action=editer&id=<?php echo $traitement['id_traitement']; ?>" class="btn-icon" title="Éditer">✏️</a>
                                    <form method="POST" action="traitement.php?action=supprimer" style="display: inline;">
                                        <input type="hidden" name="id_traitement" value="<?php echo $traitement['id_traitement']; ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ? Cette action est irréversible.')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #999;">Aucun traitement trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'ajouter' || $action === 'editer'): ?>
            <form method="POST" action="traitement.php?action=<?php echo $action === 'editer' ? 'modifier' : 'creer'; ?>" class="form-container">
                
                <?php if ($action === 'editer' && isset($data['traitement'])): ?>
                    <input type="hidden" name="id_traitement" value="<?php echo htmlspecialchars($data['traitement']['id_traitement']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nom">Nom du traitement *</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($data['traitement']['nom'] ?? ''); ?>" required maxlength="100">
                    <small>Entre 3 et 100 caractères</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type_traitement">Type de traitement *</label>
                        <select id="type_traitement" name="type_traitement" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($data['types'] ?? [] as $t): ?>
                                <option value="<?php echo htmlspecialchars($t); ?>" 
                                        <?php echo (isset($data['traitement']['type_traitement']) && $data['traitement']['type_traitement'] === $t) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst($t)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dosage">Dosage *</label>
                        <input type="text" id="dosage" name="dosage" value="<?php echo htmlspecialchars($data['traitement']['dosage'] ?? ''); ?>" required maxlength="50">
                        <small>Ex: 10mg, 5ml, etc.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duree">Durée du traitement *</label>
                        <input type="text" id="duree" name="duree" value="<?php echo htmlspecialchars($data['traitement']['duree'] ?? ''); ?>" required maxlength="50">
                        <small>Ex: 7 jours, 2 semaines, etc.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="effets_secondaires">Effets secondaires *</label>
                    <textarea id="effets_secondaires" name="effets_secondaires" required><?php echo htmlspecialchars($data['traitement']['effets_secondaires'] ?? ''); ?></textarea>
                    <small>Minimum 5 caractères</small>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($data['traitement']['description'] ?? ''); ?></textarea>
                    <small>Minimum 5 caractères</small>
                </div>

                <div class="form-actions">
                    <a href="traitement.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $action === 'editer' ? '✓ Modifier' : '✓ Ajouter'; ?>
                    </button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>

