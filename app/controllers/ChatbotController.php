<?php
require_once __DIR__ . '/../../config/Database.php';

/**
 * ChatbotController - Interface avec Azure OpenAI GPT-4o
 */
class ChatbotController
{
    private $endpoint;
    private $apiKey;
    private $deployment;
    private $apiVersion;

    public function __construct()
    {
        $this->endpoint = 'https://survive-openai.openai.azure.com/';
        $this->apiKey = '7MK8FIGgJNCSV48Jqliml9dtoqs0cutq2b2e6lNpq0DhAXA238TsJQQJ99CAACfhMk5XJ3w3AAABACOGtcXO';
        $this->deployment = 'gpt-4o';
        $this->apiVersion = '2024-02-15-preview';
    }

    /**
     * Action principale pour le chat
     */
    public static function chat()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $message = $data['message'] ?? '';

            if (empty($message)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Message vide']);
                return;
            }

            $instance = new self();
            $response = $instance->callAzureOpenAI($message);

            echo json_encode([
                'success' => true,
                'response' => $response
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Suggérer des substitutions éco-responsables
     */
    public static function suggererSubstitutions()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $recetteNom = $data['nom'] ?? '';
            $recetteDesc = $data['description'] ?? '';
            $scoreActuel = $data['score'] ?? '';

            if (empty($recetteNom)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Données de recette manquantes']);
                return;
            }

            $prompt = "Analyse cette recette : '$recetteNom'. Description : '$recetteDesc'. Son score carbone actuel est de $scoreActuel kg CO2. 
            Propose 3 substitutions ou modifications concrètes pour réduire son empreinte carbone. 
            Pour chaque suggestion, donne :
            1. Le changement à effectuer.
            2. Pourquoi c'est mieux pour l'environnement.
            3. Le pourcentage estimé de réduction du score.
            Sois précis et utilise un ton encourageant.";

            $instance = new self();
            $response = $instance->callAzureOpenAI($prompt);

            echo json_encode([
                'success' => true,
                'suggestions' => $response
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Appel à l'API Azure OpenAI
     */
    private function callAzureOpenAI($userMessage)
    {
        $url = "{$this->endpoint}openai/deployments/{$this->deployment}/chat/completions?api-version={$this->apiVersion}";

        $payload = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant expert en gestion de l\'empreinte carbone pour la plateforme ECOSAVE. Tu aides les administrateurs à comprendre les facteurs d\'émission, à analyser les recettes et à proposer des solutions écologiques. Sois concis, professionnel et encourageant.'
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'max_tokens' => 800,
            'temperature' => 0.7,
            'top_p' => 0.95,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => null
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour le développement local si nécessaire

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new Exception("CURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            $responseData = json_decode($response, true);
            $errorMsg = $responseData['error']['message'] ?? 'Erreur inconnue de l\'API OpenAI';
            throw new Exception("API Error ($httpCode): " . $errorMsg);
        }

        $responseData = json_decode($response, true);
        return $responseData['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer de réponse.';
    }
}
