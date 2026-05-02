<?php
$apiKey = 'AIzaSyBJ3fouGvg_QHFqK3de3LY_7JTULdLOGFQ';
$nomRecette = "salade";
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

$prompt = "Donne une recette de $nomRecette en JSON strict avec 3 langues (fr, en, ar). Respecte EXACTEMENT ce format JSON:
{
  \"fr\": {
    \"description\": \"...\",
    \"ingredients\": \"...\",
    \"steps\": \"...\"
  },
  \"en\": {
    \"description\": \"...\",
    \"ingredients\": \"...\",
    \"steps\": \"...\"
  },
  \"ar\": {
    \"description\": \"...\",
    \"ingredients\": \"...\",
    \"steps\": \"...\"
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
        "maxOutputTokens" => 2048
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
curl_close($ch);

$result = json_decode($response, true);
$text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'NO_TEXT';
echo "--- RAW TEXT FROM GEMINI ---\n";
echo $text . "\n";
echo "--- END RAW TEXT ---\n";

$clean = trim($text);
$clean = preg_replace('/```json|```/', '', $clean);
$dataAI = json_decode($clean, true);

echo "--- JSON DECODE RESULT ---\n";
var_dump($dataAI);
echo "--- JSON ERROR ---\n";
echo json_last_error_msg();
?>
