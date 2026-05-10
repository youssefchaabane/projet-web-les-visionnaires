<?php
require_once __DIR__ . '/../../config/ApiConfig.php';

/**
 * AiController - Gère toutes les requêtes vers l'IA Gemini
 */
class AiController
{
    private static function getApiKey()
    {
        $apiKey = defined('GOOGLE_GEMINI_API_KEY') ? GOOGLE_GEMINI_API_KEY : '';
        if (empty($apiKey)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'API KEY manquante']);
            exit;
        }
        return $apiKey;
    }

    private static function callGemini($prompt)
    {
        $apiKey = self::getApiKey();
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

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
            CURLOPT_SSL_VERIFYPEER => false // Pour XAMPP local
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur CURL: ' . $error]);
            exit;
        }

        $result = json_decode($response, true);

        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Réponse IA invalide',
                'debug' => $result
            ]);
            exit;
        }

        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    private static function parseJsonResponse($text)
    {
        $clean = trim($text);
        // Supprimer le formattage markdown ```json ... ```
        $clean = preg_replace('/```json|```/', '', $clean);
        $json = json_decode($clean, true);

        return $json ? $json : null;
    }

    // ===== 1. GENERER UNE RECETTE COMPLETE (Comme l'ancien ai.php) =====
    public static function genererRecette()
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $nomRecette = trim($input['nom_recette'] ?? '');

        if ($nomRecette === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nom recette requis']);
            return;
        }

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

        try {
            $text = self::callGemini($prompt);
            $json = self::parseJsonResponse($text);

            if ($json) {
                echo json_encode(['success' => true, 'data' => $json]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de parsing JSON depuis l\'IA',
                    'debug' => $text
                ]);
            }
        } catch (Exception $e) {
            http_response_code(200); // Renvoyer 200 pour que le frontend parse le JSON proprement
            $errMsg = $e->getMessage();
            if (strpos($errMsg, 'RESOURCE_EXHAUSTED') !== false || strpos(strtolower($errMsg), 'quota') !== false) {
                echo json_encode(['success' => false, 'message' => 'Quota API Gemini épuisé. Veuillez patienter ou utiliser une autre clé API.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur IA : ' . $errMsg]);
            }
        }
    }

    // ===== 2. GENERER UNIQUEMENT DES DETAILS (Ingrédients et Etapes) =====
    public static function genererDetails()
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $nomRecette = trim($input['nom_recette'] ?? '');

        if ($nomRecette === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nom recette requis pour générer les détails']);
            return;
        }

        // Prompt adapté uniquement pour l'entité DetailRecette (ingrédients/étapes)
        $prompt = "Je veux ajouter des détails (ingrédients et étapes) pour la recette : $nomRecette. 
        Renvoie-moi UNIQUEMENT un JSON strict avec 3 langues (fr, en, ar).
        Pour chaque langue, donne une liste d'ingrédients (avec quantité) et une liste d'étapes.
        Respecte EXACTEMENT ce format JSON, sans texte autour :
        {
          \"fr\": {
            \"ingredients\": [\"200g de farine\", \"3 oeufs\"],
            \"steps\": [\"Mélanger la farine et les oeufs\", \"Cuire au four\"]
          },
          \"en\": {
            \"ingredients\": [\"200g flour\", \"3 eggs\"],
            \"steps\": [\"Mix flour and eggs\", \"Bake in oven\"]
          },
          \"ar\": {
            \"ingredients\": [\"200 جرام دقيق\", \"3 بيضات\"],
            \"steps\": [\"اخلط الدقيق والبيض\", \"اخبز في الفرن\"]
          }
        }";

        try {
            $text = self::callGemini($prompt);
            $json = self::parseJsonResponse($text);

            if ($json) {
                echo json_encode(['success' => true, 'data' => $json]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de parsing JSON depuis l\'IA pour les détails',
                    'debug' => $text
                ]);
            }
        } catch (Exception $e) {
            http_response_code(200); // Renvoyer 200 pour que le frontend parse le JSON proprement
            $errMsg = $e->getMessage();
            if (strpos($errMsg, 'RESOURCE_EXHAUSTED') !== false || strpos(strtolower($errMsg), 'quota') !== false) {
                echo json_encode(['success' => false, 'message' => 'Quota API Gemini épuisé. Impossible de générer les détails.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur IA : ' . $errMsg]);
            }
        }
    }

    // ===== 3. GENERER UNE IMAGE VIA IA (Pollinations.ai) =====
    public static function genererImage()
    {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        $nomRecette = trim($input['nom_recette'] ?? '');

        if ($nomRecette === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nom de recette requis pour générer une image']);
            return;
        }

        // 1. Demander à Gemini de créer un prompt d'image très détaillé (en anglais pour de meilleurs résultats)
        $promptGemini = "Write a highly detailed DALL-E prompt in English for a professional food photography of the dish: '$nomRecette'. 
        The image should look delicious, well-lit, cinematic, and appetizing. Do not include any text in the image.
        Return ONLY the prompt string, nothing else.";

        // 1. Demander à Gemini de créer un prompt détaillé (avec gestion d'erreur)
        $imagePrompt = "";
        try {
            $imagePrompt = self::callGemini($promptGemini);
            // Nettoyage extrême : on ne garde que les lettres, chiffres, virgules et espaces
            $imagePrompt = preg_replace('/[^a-zA-Z0-9,\s]/', '', $imagePrompt);
            $imagePrompt = trim(preg_replace('/\s+/', ' ', $imagePrompt));
            
            if (strlen($imagePrompt) > 300) {
                $imagePrompt = substr($imagePrompt, 0, 300);
            }
        } catch (Exception $e) {
            // Si Gemini échoue (ex: Quota épuisé RESOURCE_EXHAUSTED), on ignore l'erreur
            // On s'appuiera sur le prompt par défaut ci-dessous.
            $imagePrompt = ""; 
        }
        
        // Sécurité de base si Gemini renvoie quelque chose de vide ou plante
        if (empty($imagePrompt)) {
            $imagePrompt = "delicious high quality food photography of " . preg_replace('/[^a-zA-Z0-9\s]/', '', $nomRecette) . " cinematic lighting";
        }

        // 2. Générer l'URL de l'API Pollinations
        $seed = mt_rand(1, 999999);
        $pollinationsUrl = 'https://image.pollinations.ai/prompt/' . rawurlencode($imagePrompt) . '?width=800&height=600&nologo=true&seed=' . $seed;

        // 3. Télécharger l'image côté serveur pour éviter tout blocage du navigateur (CORS, AdBlockers, etc.)
        $context = stream_context_create([
            "http" => [
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
            ]
        ]);
        
        $imageData = @file_get_contents($pollinationsUrl, false, $context);

        if ($imageData === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "Impossible de télécharger l'image depuis l'IA."]);
            return;
        }

        // 4. Sauvegarder l'image localement dans le dossier uploads
        $fileName = 'ai_recipe_' . time() . '_' . $seed . '.jpg';
        $uploadDir = __DIR__ . '/../../uploads/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filePath = $uploadDir . $fileName;
        file_put_contents($filePath, $imageData);

        // 5. Renvoyer l'URL locale au frontend (chemin relatif utilisable par les vues dans app/views/)
        // L'URL locale sera du type '../../uploads/nom_fichier.jpg'
        $localUrl = '../../uploads/' . $fileName;

        echo json_encode([
            'success' => true, 
            'image_url' => $localUrl,
            'debug_prompt' => $imagePrompt
        ]);
    }
}
?>
