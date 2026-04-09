<?php
session_start();
require_once __DIR__ . '/config/Database.php';

$pdo = Database::getInstance()->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$message = '';
$erreurs = [];
$data = [];

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'creer':
            $id_allergie = intval($_POST['id_allergie'] ?? 0);
            $id_traitement = intval($_POST['id_traitement'] ?? 0);
            
            if (!$id_allergie || !$id_traitement) {
                $erreurs[] = 'Veuillez sélectionner une allergie et un traitement';
            } else {
                // Vérifier si l'association existe déjà
                $stmt = $pdo->prepare("SELECT id FROM allergie_traitement WHERE id_allergie = ? AND id_traitement = ?");
                $stmt->execute([$id_allergie, $id_traitement]);
                
                if ($stmt->rowCount() > 0) {
                    $erreurs[] = 'Cette association existe déjà';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO allergie_traitement (id_allergie, id_traitement, date_ajout) VALUES (?, ?, NOW())");
                    if ($stmt->execute([$id_allergie, $id_traitement])) {
                        $message = "✓ Association créée avec succès";
                        $action = 'liste';
                    } else {
                        $erreurs[] = 'Erreur lors de la création de l\'association';
                    }
                }
            }
            break;

        case 'supprimer':
            $id = intval($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM allergie_traitement WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = "✓ Association supprimée avec succès";
            } else {
                $erreurs[] = 'Erreur lors de la suppression';
            }
            $action = 'liste';
            break;
    }
}

// Get statistics
$stmt_assoc = $pdo->prepare("SELECT COUNT(*) as total FROM allergie_traitement");
$stmt_assoc->execute();
$result = $stmt_assoc->fetch(PDO::FETCH_ASSOC);
$total_associations = $result['total'];

$stmt_allerg = $pdo->prepare("SELECT COUNT(*) as total FROM allergie");
$stmt_allerg->execute();
$result = $stmt_allerg->fetch(PDO::FETCH_ASSOC);
$total_allergies = $result['total'];

$stmt_trait = $pdo->prepare("SELECT COUNT(*) as total FROM traitement");
$stmt_trait->execute();
$result = $stmt_trait->fetch(PDO::FETCH_ASSOC);
$total_traitements = $result['total'];

// Load data based on action
if ($action === 'liste') {
    // Get associations
    $stmt = $pdo->prepare("
        SELECT at.id, a.nom as allergie_nom, t.nom as traitement_nom, 
               t.type_traitement, at.date_ajout
        FROM allergie_traitement at
        JOIN allergie a ON at.id_allergie = a.id_allergie
        JOIN traitement t ON at.id_traitement = t.id_traitement
        ORDER BY at.date_ajout DESC
    ");
    $stmt->execute();
    $data['associations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($action === 'ajouter') {
    // Get allergies and treatments for form
    $stmt = $pdo->prepare("SELECT id_allergie, nom FROM allergie ORDER BY nom");
    $stmt->execute();
    $data['allergies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT id_traitement, nom FROM traitement ORDER BY nom");
    $stmt->execute();
    $data['traitements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Associations Allergies-Traitements</title>
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

        .form-group select {
            cursor: pointer;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
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
            <a href="associations_public.php">👁️ Voir FrontOffice</a>
        </div>
    </div>

    <div class="main">
        <h1>🔗 <?php echo ['liste' => 'Associations Allergie-Traitement', 'ajouter' => 'Créer une association'][$action] ?? 'Associations'; ?></h1>

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
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($action === 'liste'): ?>
            <div class="cards">
                <div class="card">
                    <h3>Total Associations</h3>
                    <p><?php echo $total_associations; ?></p>
                </div>

                <div class="card">
                    <h3>Allergies</h3>
                    <p><?php echo $total_allergies; ?></p>
                </div>

                <div class="card">
                    <h3>Traitements</h3>
                    <p><?php echo $total_traitements; ?></p>
                </div>
            </div>

            <div class="section-header">
                <h2>Associations (<?php echo count($data['associations'] ?? []); ?>)</h2>
                <a href="associations.php?action=ajouter" class="btn btn-primary">➕ Ajouter</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Allergie</th>
                        <th>Traitement</th>
                        <th>Type</th>
                        <th>Date d'association</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['associations'])): ?>
                        <?php foreach ($data['associations'] as $assoc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($assoc['allergie_nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($assoc['traitement_nom']); ?></td>
                                <td><span class="badge badge-<?php echo strtolower($assoc['type_traitement']); ?>"><?php echo htmlspecialchars(ucfirst($assoc['type_traitement'])); ?></span></td>
                                <td><?php echo date('d/m/Y', strtotime($assoc['date_ajout'])); ?></td>
                                <td class="action-buttons">
                                    <form method="POST" action="associations.php?action=supprimer" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $assoc['id']; ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr ? Cette action est irréversible.')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Aucune association trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'ajouter'): ?>
            <form method="POST" action="associations.php?action=creer" class="form-container">
                
                <div class="form-group">
                    <label for="id_allergie">Sélectionner une allergie *</label>
                    <select id="id_allergie" name="id_allergie" required>
                        <option value="">-- Choisir une allergie --</option>
                        <?php foreach ($data['allergies'] ?? [] as $allergie): ?>
                            <option value="<?php echo intval($allergie['id_allergie']); ?>">
                                <?php echo htmlspecialchars($allergie['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_traitement">Sélectionner un traitement *</label>
                    <select id="id_traitement" name="id_traitement" required>
                        <option value="">-- Choisir un traitement --</option>
                        <?php foreach ($data['traitements'] ?? [] as $traitement): ?>
                            <option value="<?php echo intval($traitement['id_traitement']); ?>">
                                <?php echo htmlspecialchars($traitement['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="associations.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">✓ Créer l'association</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
