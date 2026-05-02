<?php
header('Content-Type: application/json; charset=utf-8');

function sendJson($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* =========================
   LOAD API KEY
========================= */
require_once __DIR__ . '/config/ApiConfig.php';

$apiKey = defined('GOOGLE_GEMINI_API_KEY') ? GOOGLE_GEMINI_API_KEY : '';

if (empty($apiKey)) {
    sendJson([
        'success' => false,
        'message' => 'API KEY manquante'
    ], 500);
}

/* =========================
   GET DATA
========================= */
$input = json_decode(file_get_contents("php://input"), true);

$nomRecette = trim($input['nom_recette'] ?? '');

if ($nomRecette === '') {
    sendJson([
        'success' => false,
        'message' => 'Nom recette requis'
    ], 400);
}

/* =========================
   GEMINI API
========================= */
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

$prompt = "Donne une recette de $nomRecette en JSON strict avec 3 langues (fr, en, ar). Pour les ingrédients et étapes, utilise un TABLEAU de chaînes de caractères (array of strings) pour garantir un JSON valide. Respecte EXACTEMENT ce format JSON:
{
  \"fr\": {
    \"description\": \"...\",
    \"ingredients\": [\"...\", \"...\"],
    \"steps\": [\"...\", \"...\"]
  },
  \"en\": {
    \"description\": \"...\",
    \"ingredients\": [\"...\", \"...\"],
    \"steps\": [\"...\", \"...\"]
  },
  \"ar\": {
    \"description\": \"...\",
    \"ingredients\": [\"...\", \"...\"],
    \"steps\": [\"...\", \"...\"]
  }
}";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 8192
    ]
];

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    sendJson([
        'success' => false,
        'message' => 'Erreur CURL: ' . $error
    ], 500);
}

$result = json_decode($response, true);

if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    sendJson([
        'success' => false,
        'message' => 'Réponse invalide',
        'debug' => $result
    ], 500);
}

/* =========================
   PARSE RESULT
========================= */
$text = $result['candidates'][0]['content']['parts'][0]['text'];


$clean = trim($text);
$clean = preg_replace('/```json|```/', '', $clean);

file_put_contents(__DIR__ . '/ai_debug.txt', "RAW GEMINI OUTPUT:\n" . $text . "\n\nCLEANED:\n" . $clean);

$json = json_decode($clean, true);

if ($json) {
    sendJson([
        'success' => true,
        'data' => $json
    ]);
}

/* fallback en cas d'erreur de parsing JSON (souvent dû à une troncature) */
sendJson([
    'success' => false,
    'message' => 'Erreur : l\'IA a renvoyé un format invalide ou le texte a été tronqué.',
    'debug' => $clean
], 500);