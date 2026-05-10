<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user_id'])) { header('Content-Type: application/json'); echo json_encode(['error'=>'Non autorisé']); exit; }

require_once __DIR__ . '/../config/config.php';
$pdo = config::getConnexion();
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$action = $_REQUEST['action'] ?? '';

if (!function_exists('ej')) { function ej($d){ header('Content-Type: application/json'); echo json_encode($d); } }

// ── RECETTES CRUD ────────────────────────────────────────────

if ($action === 'recettes_getAll') {
    $q = trim($_GET['q'] ?? '');
    $diff = trim($_GET['difficulte'] ?? '');
    $sort = in_array($_GET['sort']??'', ['nom','difficulte','calories_totales','date_creation']) ? $_GET['sort'] : 'date_creation';
    $sql = "SELECT r.*, (SELECT COUNT(*) FROM rec_detail_recette d WHERE d.id_recette=r.id_recette) as nb_details FROM rec_recette r WHERE 1=1";
    $p = [];
    if ($q) { $sql .= " AND (r.nom LIKE ? OR r.description LIKE ?)"; $p[] = "%$q%"; $p[] = "%$q%"; }
    if ($diff) { $sql .= " AND r.difficulte = ?"; $p[] = $diff; }
    $sql .= " ORDER BY $sort DESC";
    $st = $pdo->prepare($sql); $st->execute($p);
    ej(['success'=>true,'data'=>$st->fetchAll()]); exit;
}

if ($action === 'recettes_getOne') {
    $id = (int)($_GET['id']??0);
    $r = $pdo->prepare("SELECT * FROM rec_recette WHERE id_recette=?"); $r->execute([$id]);
    $rec = $r->fetch();
    $d = $pdo->prepare("SELECT * FROM rec_detail_recette WHERE id_recette=?"); $d->execute([$id]);
    $details = $d->fetchAll();
    ej(['success'=>!!$rec,'recette'=>$rec,'details'=>$details]); exit;
}

if ($action === 'recettes_creer' && $isAdmin) {
    $nom = trim($_POST['nom']??'');
    if (!$nom) { ej(['success'=>false,'message'=>'Nom requis']); exit; }
    $pdo->prepare("INSERT INTO rec_recette (nom,description,nombre_personnes,temps_preparation,temps_cuisson,difficulte,calories_totales,image_url,id_user) VALUES (?,?,?,?,?,?,?,?,?)")
        ->execute([$nom, $_POST['description']??'', (int)($_POST['nombre_personnes']??2), (int)($_POST['temps_preparation']??0), (int)($_POST['temps_cuisson']??0), $_POST['difficulte']??'moyen', (int)($_POST['calories_totales']??0), $_POST['image_url']??null, $_SESSION['user_id']]);
    $id = (int)$pdo->lastInsertId();
    // Save ingredients
    if (!empty($_POST['ingredients'])) {
        $ings = json_decode($_POST['ingredients'], true) ?? [];
        $st = $pdo->prepare("INSERT INTO rec_detail_recette (id_recette,ingredient,quantite,etape) VALUES (?,?,?,?)");
        foreach ($ings as $ing) { $st->execute([$id, $ing['ingredient']??'', $ing['quantite']??'', $ing['etape']??'']); }
    }
    ej(['success'=>true,'message'=>'Recette créée','id'=>$id]); exit;
}

if ($action === 'recettes_modifier' && $isAdmin) {
    $id = (int)($_POST['id']??0);
    if (!$id) { ej(['success'=>false,'message'=>'ID requis']); exit; }
    $pdo->prepare("UPDATE rec_recette SET nom=?,description=?,nombre_personnes=?,temps_preparation=?,temps_cuisson=?,difficulte=?,calories_totales=?,image_url=? WHERE id_recette=?")
        ->execute([trim($_POST['nom']??''), $_POST['description']??'', (int)($_POST['nombre_personnes']??2), (int)($_POST['temps_preparation']??0), (int)($_POST['temps_cuisson']??0), $_POST['difficulte']??'moyen', (int)($_POST['calories_totales']??0), $_POST['image_url']??null, $id]);
    ej(['success'=>true,'message'=>'Recette modifiée']); exit;
}

if ($action === 'recettes_supprimer' && $isAdmin) {
    $id = (int)($_POST['id']??0);
    $pdo->prepare("DELETE FROM rec_recette WHERE id_recette=?")->execute([$id]);
    ej(['success'=>true,'message'=>'Recette supprimée']); exit;
}

// ── DÉTAILS (ingrédients / étapes) ───────────────────────────

if ($action === 'details_getAll') {
    $id = (int)($_GET['id_recette']??0);
    $st = $pdo->prepare("SELECT * FROM rec_detail_recette WHERE id_recette=?"); $st->execute([$id]);
    ej(['success'=>true,'data'=>$st->fetchAll()]); exit;
}

if ($action === 'details_ajouter' && $isAdmin) {
    $id = (int)($_POST['id_recette']??0);
    $pdo->prepare("INSERT INTO rec_detail_recette (id_recette,ingredient,quantite,etape) VALUES (?,?,?,?)")
        ->execute([$id, $_POST['ingredient']??'', $_POST['quantite']??'', $_POST['etape']??'']);
    ej(['success'=>true,'message'=>'Détail ajouté','id'=>$pdo->lastInsertId()]); exit;
}

if ($action === 'details_supprimer' && $isAdmin) {
    $id = (int)($_POST['id_detail']??0);
    $pdo->prepare("DELETE FROM rec_detail_recette WHERE id_detail=?")->execute([$id]);
    ej(['success'=>true,'message'=>'Détail supprimé']); exit;
}

// ── STATS ─────────────────────────────────────────────────────

if ($action === 'stats') {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM rec_recette")->fetchColumn();
    $byDiff = $pdo->query("SELECT difficulte, COUNT(*) as cnt FROM rec_recette GROUP BY difficulte")->fetchAll();
    $avgCal = round((float)$pdo->query("SELECT AVG(calories_totales) FROM rec_recette")->fetchColumn());
    $avgTime = round((float)$pdo->query("SELECT AVG(temps_preparation+temps_cuisson) FROM rec_recette")->fetchColumn());
    ej(['success'=>true,'total'=>$total,'byDiff'=>$byDiff,'avgCal'=>$avgCal,'avgTime'=>$avgTime]); exit;
}

// ── IA GÉNÉRATION (Gemini 2.5 Flash) ─────────────────────────

if ($action === 'ai_generer') {
    $nom = trim($_POST['nom_recette']??'');
    if (!$nom) { ej(['success'=>false,'message'=>'Nom requis']); exit; }

    $payload = json_encode([
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            ['role'=>'system','content'=>'Tu es un chef cuisinier expert. Réponds UNIQUEMENT avec du JSON valide, sans markdown, sans texte autour.'],
            ['role'=>'user','content'=>'Génère une recette pour "'.$nom.'" en français. Réponds avec ce JSON : {"description":"...","nombre_personnes":4,"temps_preparation":15,"temps_cuisson":30,"difficulte":"moyen","calories_totales":450,"ingredients":[{"ingredient":"...","quantite":"..."}],"etapes":["étape 1","étape 2","étape 3"]}']
        ],
        'temperature' => 0.7,
        'max_tokens' => 1500,
        'response_format' => ['type' => 'json_object']
    ]);
    $ch = curl_init(config::GROQ_ENDPOINT);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.config::GROQ_API_KEY],
        CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER=>false]);
    $resp = curl_exec($ch); $code = curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
    if ($code===200) {
        $d = json_decode($resp,true);
        $text = $d['choices'][0]['message']['content']??'';
        $json = json_decode($text,true);
        if (!$json) { $json = json_decode(preg_replace('/\r\n|\r|\n/',' ',$text),true); }
        if ($json) { ej(['success'=>true,'data'=>$json]); }
        else { ej(['success'=>false,'message'=>'Parsing échoué: '.json_last_error_msg()]); }
    } else { $e=json_decode($resp,true); ej(['success'=>false,'message'=>'IA: '.($e['error']['message']??'HTTP '.$code)]); }
    exit;
}

if ($action === 'ai_conseil') {
    $id = (int)($_POST['id_recette']??0);
    $r = $pdo->prepare("SELECT r.*, GROUP_CONCAT(d.ingredient SEPARATOR ', ') as ingredients FROM rec_recette r LEFT JOIN rec_detail_recette d ON r.id_recette=d.id_recette WHERE r.id_recette=? GROUP BY r.id_recette");
    $r->execute([$id]); $rec = $r->fetch();
    if (!$rec) { ej(['success'=>false,'message'=>'Recette introuvable']); exit; }

    $prompt = "Recette \"{$rec['nom']}\" ({$rec['difficulte']}, {$rec['calories_totales']}kcal, {$rec['nombre_personnes']} pers.).\n"
            . "Ingrédients: {$rec['ingredients']}\n\n"
            . "Donne 3 conseils de chef: 1)Présentation 2)Substitution saine 3)Astuce cuisson. Français, concis, emojis 🍴";

    $payload = json_encode([
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            ['role'=>'system','content'=>'Tu es un chef cuisinier expert. Réponds en français, de façon concise.'],
            ['role'=>'user','content'=>$prompt]
        ],
        'temperature' => 0.7, 'max_tokens' => 600
    ]);
    $ch = curl_init(config::GROQ_ENDPOINT);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.config::GROQ_API_KEY],
        CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER=>false]);
    $resp = curl_exec($ch); $code = curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
    if ($code===200) { $d=json_decode($resp,true); ej(['success'=>true,'conseil'=>$d['choices'][0]['message']['content']??'']); }
    else { ej(['success'=>false,'message'=>'IA indisponible (HTTP '.$code.')']); }
    exit;
}

// ── IMAGE IA (Groq prompt → Pollinations.ai) ──────────────────

if ($action === 'ai_image') {
    $nom = trim($_POST['nom_recette']??'');
    if (!$nom) { ej(['success'=>false,'message'=>'Nom requis']); exit; }

    // Étape 1 — Groq génère un prompt d'image DALL-E détaillé (en anglais)
    $imagePrompt = '';
    $pPayload = json_encode([
        'model' => 'llama-3.1-8b-instant',
        'messages' => [
            ['role'=>'system','content'=>'You are a food photography expert. Return ONLY a DALL-E image prompt string, nothing else.'],
            ['role'=>'user','content'=>"Write a highly detailed DALL-E prompt for professional food photography of: '$nom'. Delicious, well-lit, cinematic, appetizing. No text in image. Return ONLY the prompt."]
        ],
        'temperature' => 0.7, 'max_tokens' => 200
    ]);
    $ch = curl_init(config::GROQ_ENDPOINT);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$pPayload,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.config::GROQ_API_KEY],
        CURLOPT_TIMEOUT=>15,CURLOPT_SSL_VERIFYPEER=>false]);
    $pr = curl_exec($ch); $pc = curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
    if ($pc===200) {
        $pd = json_decode($pr,true);
        $imagePrompt = $pd['choices'][0]['message']['content']??'';
        $imagePrompt = preg_replace('/[^a-zA-Z0-9,\s]/','',$imagePrompt);
        $imagePrompt = trim(preg_replace('/\s+/',' ',$imagePrompt));
        if (strlen($imagePrompt)>300) $imagePrompt = substr($imagePrompt,0,300);
    }

    // Fallback
    if (empty($imagePrompt)) {
        $imagePrompt = 'delicious high quality food photography of '.preg_replace('/[^a-zA-Z0-9\s]/','',$nom).' cinematic lighting';
    }

    // Étape 2 — Pollinations.ai génère l'image
    $seed = mt_rand(1,999999);
    $url = 'https://image.pollinations.ai/prompt/'.rawurlencode($imagePrompt).'?width=800&height=600&nologo=true&seed='.$seed;
    $ctx = stream_context_create(['http'=>['header'=>'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)']]);
    $imageData = @file_get_contents($url, false, $ctx);
    if (!$imageData) { ej(['success'=>false,'message'=>"Impossible de générer l'image"]); exit; }

    $dir = __DIR__.'/../uploads/recettes/';
    if (!is_dir($dir)) mkdir($dir,0777,true);
    $fname = 'rec_'.time().'_'.$seed.'.jpg';
    file_put_contents($dir.$fname,$imageData);
    ej(['success'=>true,'image_url'=>'../uploads/recettes/'.$fname,'debug_prompt'=>$imagePrompt]);
    exit;
}

ej(['error'=>'Action invalide']);
