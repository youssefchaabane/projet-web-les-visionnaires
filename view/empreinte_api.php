<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../config/config.php';
$pdo = config::getConnexion();

if (!function_exists('ej')) {
    function ej(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// ─── RECETTES ────────────────────────────────────────────────────────────────

if ($action === 'recettes_getAll') {
    $search = trim($_GET['q'] ?? '');
    if ($search !== '') {
        $st = $pdo->prepare("SELECT * FROM eco_recette WHERE nom LIKE ? OR description LIKE ? ORDER BY nom ASC");
        $st->execute(["%$search%", "%$search%"]);
    } else {
        $st = $pdo->query("SELECT * FROM eco_recette ORDER BY nom ASC");
    }
    ej(['success' => true, 'data' => $st->fetchAll()]);
    exit;
}

if ($action === 'recettes_creer' && $isAdmin) {
    $nom = trim($_POST['nom'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($nom === '') { ej(['success' => false, 'message' => 'Nom requis']); exit; }
    $pdo->prepare("INSERT INTO eco_recette (nom, description) VALUES (?, ?)")->execute([$nom, $desc]);
    ej(['success' => true, 'message' => 'Recette ajoutée', 'id' => $pdo->lastInsertId()]);
    exit;
}

if ($action === 'recettes_modifier' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    $nom = trim($_POST['nom'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if (!$id || $nom === '') { ej(['success' => false, 'message' => 'Données invalides']); exit; }
    $pdo->prepare("UPDATE eco_recette SET nom=?, description=? WHERE id_recette=?")->execute([$nom, $desc, $id]);
    ej(['success' => true, 'message' => 'Recette modifiée']);
    exit;
}

if ($action === 'recettes_supprimer' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { ej(['success' => false, 'message' => 'ID requis']); exit; }
    $pdo->prepare("DELETE FROM eco_recette WHERE id_recette=?")->execute([$id]);
    ej(['success' => true, 'message' => 'Recette supprimée']);
    exit;
}

// ─── FACTEURS D'ÉMISSION ─────────────────────────────────────────────────────

if ($action === 'facteurs_getAll') {
    $search = trim($_GET['q'] ?? '');
    if ($search !== '') {
        $st = $pdo->prepare("SELECT * FROM eco_facteur_emission WHERE categorie_aliment LIKE ? OR source_donnee LIKE ? ORDER BY categorie_aliment ASC");
        $st->execute(["%$search%", "%$search%"]);
    } else {
        $st = $pdo->query("SELECT * FROM eco_facteur_emission ORDER BY co2_par_kg DESC");
    }
    ej(['success' => true, 'data' => $st->fetchAll()]);
    exit;
}

if ($action === 'facteurs_creer' && $isAdmin) {
    $cat = trim($_POST['categorie_aliment'] ?? '');
    $co2 = (float)($_POST['co2_par_kg'] ?? 0);
    $src = trim($_POST['source_donnee'] ?? '');
    $date = trim($_POST['date_derniere_maj'] ?? date('Y-m-d'));
    if ($cat === '') { ej(['success' => false, 'message' => 'Catégorie requise']); exit; }
    $pdo->prepare("INSERT INTO eco_facteur_emission (categorie_aliment, co2_par_kg, source_donnee, date_derniere_maj) VALUES (?,?,?,?)")->execute([$cat, $co2, $src, $date]);
    ej(['success' => true, 'message' => 'Facteur ajouté', 'id' => $pdo->lastInsertId()]);
    exit;
}

if ($action === 'facteurs_modifier' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    $cat = trim($_POST['categorie_aliment'] ?? '');
    $co2 = (float)($_POST['co2_par_kg'] ?? 0);
    $src = trim($_POST['source_donnee'] ?? '');
    $date = trim($_POST['date_derniere_maj'] ?? date('Y-m-d'));
    if (!$id || $cat === '') { ej(['success' => false, 'message' => 'Données invalides']); exit; }
    $pdo->prepare("UPDATE eco_facteur_emission SET categorie_aliment=?, co2_par_kg=?, source_donnee=?, date_derniere_maj=? WHERE id_facteur=?")->execute([$cat, $co2, $src, $date, $id]);
    ej(['success' => true, 'message' => 'Facteur modifié']);
    exit;
}

if ($action === 'facteurs_supprimer' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { ej(['success' => false, 'message' => 'ID requis']); exit; }
    $pdo->prepare("DELETE FROM eco_facteur_emission WHERE id_facteur=?")->execute([$id]);
    ej(['success' => true, 'message' => 'Facteur supprimé']);
    exit;
}

// ─── ANALYSES CARBONE ────────────────────────────────────────────────────────

if ($action === 'analyses_getAll') {
    $search = trim($_GET['q'] ?? '');
    if ($search !== '') {
        $st = $pdo->prepare("SELECT a.*, r.nom as nom_recette FROM eco_analyse_carbone a LEFT JOIN eco_recette r ON a.id_recette=r.id_recette WHERE r.nom LIKE ? OR a.methode_calcul LIKE ? OR a.niveau_impact LIKE ? ORDER BY a.date_calcul DESC");
        $st->execute(["%$search%", "%$search%", "%$search%"]);
    } else {
        $st = $pdo->query("SELECT a.*, r.nom as nom_recette FROM eco_analyse_carbone a LEFT JOIN eco_recette r ON a.id_recette=r.id_recette ORDER BY a.date_calcul DESC");
    }
    ej(['success' => true, 'data' => $st->fetchAll()]);
    exit;
}

if ($action === 'analyses_creer' && $isAdmin) {
    $score = (float)($_POST['score_co2_total'] ?? 0);
    $impact = trim($_POST['niveau_impact'] ?? 'moyen');
    $date = trim($_POST['date_calcul'] ?? date('Y-m-d'));
    $methode = trim($_POST['methode_calcul'] ?? '');
    $idR = (int)($_POST['id_recette'] ?? 0);
    if (!$idR) { ej(['success' => false, 'message' => 'Recette requise']); exit; }
    $pdo->prepare("INSERT INTO eco_analyse_carbone (score_co2_total, niveau_impact, date_calcul, methode_calcul, id_recette) VALUES (?,?,?,?,?)")->execute([$score, $impact, $date, $methode, $idR]);
    ej(['success' => true, 'message' => 'Analyse ajoutée', 'id' => $pdo->lastInsertId()]);
    exit;
}

if ($action === 'analyses_modifier' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    $score = (float)($_POST['score_co2_total'] ?? 0);
    $impact = trim($_POST['niveau_impact'] ?? 'moyen');
    $date = trim($_POST['date_calcul'] ?? date('Y-m-d'));
    $methode = trim($_POST['methode_calcul'] ?? '');
    $idR = (int)($_POST['id_recette'] ?? 0);
    if (!$id || !$idR) { ej(['success' => false, 'message' => 'Données invalides']); exit; }
    $pdo->prepare("UPDATE eco_analyse_carbone SET score_co2_total=?, niveau_impact=?, date_calcul=?, methode_calcul=?, id_recette=? WHERE id_analyse=?")->execute([$score, $impact, $date, $methode, $idR, $id]);
    ej(['success' => true, 'message' => 'Analyse modifiée']);
    exit;
}

if ($action === 'analyses_supprimer' && $isAdmin) {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { ej(['success' => false, 'message' => 'ID requis']); exit; }
    $pdo->prepare("DELETE FROM eco_analyse_carbone WHERE id_analyse=?")->execute([$id]);
    ej(['success' => true, 'message' => 'Analyse supprimée']);
    exit;
}

// ─── STATS ───────────────────────────────────────────────────────────────────

if ($action === 'stats') {
    $totalRec = (int)$pdo->query("SELECT COUNT(*) FROM eco_recette")->fetchColumn();
    $totalFac = (int)$pdo->query("SELECT COUNT(*) FROM eco_facteur_emission")->fetchColumn();
    $totalAna = (int)$pdo->query("SELECT COUNT(*) FROM eco_analyse_carbone")->fetchColumn();
    $avgScore = (float)($pdo->query("SELECT AVG(score_co2_total) FROM eco_analyse_carbone")->fetchColumn() ?? 0);
    $impactCounts = $pdo->query("SELECT niveau_impact, COUNT(*) as cnt FROM eco_analyse_carbone GROUP BY niveau_impact")->fetchAll();
    ej(['success' => true, 'recettes' => $totalRec, 'facteurs' => $totalFac, 'analyses' => $totalAna, 'avg_score' => round($avgScore, 2), 'impacts' => $impactCounts]);
    exit;
}

// ─── AI SUGGESTION ───────────────────────────────────────────────────────────

if ($action === 'ai_suggestion') {
    $idR = (int)($_POST['id_recette'] ?? 0);
    if (!$idR) { ej(['success' => false, 'message' => 'Recette requise']); exit; }

    $st = $pdo->prepare("SELECT r.nom, r.description, a.score_co2_total, a.niveau_impact FROM eco_recette r LEFT JOIN eco_analyse_carbone a ON r.id_recette = a.id_recette WHERE r.id_recette = ? ORDER BY a.date_calcul DESC LIMIT 1");
    $st->execute([$idR]);
    $row = $st->fetch();

    if (!$row) { ej(['success' => false, 'message' => 'Recette introuvable']); exit; }

    $nom = $row['nom'];
    $desc = $row['description'] ?? '';
    $score = $row['score_co2_total'] ?? '?';
    $impact = $row['niveau_impact'] ?? '?';

    $prompt = "Tu es un expert en développement durable et empreinte carbone alimentaire.\n"
        . "Recette : \"$nom\"\n"
        . "Description : $desc\n"
        . "Score CO2 actuel : {$score} kg CO2eq\n"
        . "Niveau d'impact : $impact\n\n"
        . "Propose 3 substitutions concrètes pour réduire l'empreinte carbone de cette recette.\n"
        . "Pour chaque substitution : 1) l'ingrédient à remplacer et par quoi, 2) pourquoi c'est mieux pour la planète, 3) le % estimé de réduction.\n"
        . "Sois concis, bienveillant et pratique. Réponds en français avec des emojis 🌿.";

    $payload = json_encode([
        'model' => 'llama-3.1-8b-instant',
        'messages' => [
            ['role' => 'system', 'content' => 'Tu es un assistant expert en empreinte carbone alimentaire pour la plateforme ECOSAVE. Réponds toujours en français.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 800
    ]);

    $ch = curl_init(config::GROQ_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . config::GROQ_API_KEY],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 200) {
        $data = json_decode($resp, true);
        $text = $data['choices'][0]['message']['content'] ?? 'Aucune suggestion disponible.';
        ej(['success' => true, 'suggestion' => $text]);
    } else {
        ej(['success' => false, 'message' => 'Service IA indisponible (HTTP ' . $code . ')']);
    }
    exit;
}

// ─── AI CHAT (ADMIN CHATBOT) ─────────────────────────────────────────────────

if ($action === 'ai_chat' && $isAdmin) {
    $message = trim($_POST['message'] ?? '');
    if ($message === '') { ej(['success' => false, 'message' => 'Message vide']); exit; }

    // Inject live DB context into system prompt
    $totalRec = (int)$pdo->query("SELECT COUNT(*) FROM eco_recette")->fetchColumn();
    $totalFac = (int)$pdo->query("SELECT COUNT(*) FROM eco_facteur_emission")->fetchColumn();
    $avgScore = round((float)($pdo->query("SELECT AVG(score_co2_total) FROM eco_analyse_carbone")->fetchColumn() ?? 0), 2);
    $topFacteurs = $pdo->query("SELECT categorie_aliment, co2_par_kg FROM eco_facteur_emission ORDER BY co2_par_kg DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $topStr = implode(', ', array_map(fn($f) => "{$f['categorie_aliment']} ({$f['co2_par_kg']} kg CO2/kg)", $topFacteurs));

    $systemPrompt = "Tu es ECOSAVE Assistant, un expert en gestion de l'empreinte carbone alimentaire.\n"
        . "Données actuelles de la plateforme :\n"
        . "- {$totalRec} recettes enregistrées\n"
        . "- {$totalFac} facteurs d'émission référencés\n"
        . "- Score CO2 moyen des analyses : {$avgScore} kg\n"
        . "- Top émetteurs : {$topStr}\n\n"
        . "Aide l'administrateur à comprendre les données, interpréter les scores carbone, "
        . "trouver des pistes d'amélioration et gérer la plateforme ECOSAVE. Sois concis et professionnel. Réponds en français.";

    $payload = json_encode([
        'model' => 'llama-3.1-8b-instant',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $message]
        ],
        'temperature' => 0.7,
        'max_tokens' => 600
    ]);

    $ch = curl_init(config::GROQ_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . config::GROQ_API_KEY],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 200) {
        $data = json_decode($resp, true);
        ej(['success' => true, 'reply' => $data['choices'][0]['message']['content'] ?? 'Pas de réponse.']);
    } else {
        ej(['success' => false, 'message' => 'Service IA indisponible (HTTP ' . $code . ')']);
    }
    exit;
}

ej(['error' => 'Action invalide']);
