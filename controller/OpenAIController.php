<?php
/**
 * OpenAIController - Gestion des interactions avec l'API OpenAI Azure
 */
class OpenAIController {
    private $endpoint;
    private $apiKey;
    private $deployment;
    private $apiVersion;

    public function __construct() {
        $this->endpoint = 'https://survive-openai.openai.azure.com/';
        $this->apiKey = '7MK8FIGgJNCSV48Jqliml9dtoqs0cutq2b2e6lNpq0DhAXA238TsJQQJ99CAACfhMk5XJ3w3AAABACOGtcXO';
        $this->deployment = 'gpt-4o';
        $this->apiVersion = '2024-02-15-preview';
    }

    /**
     * Générer une description pour une catégorie
     */
    public function generateCategoryDescription($categoryName) {
        try {
            $url = $this->endpoint . 'openai/deployments/' . $this->deployment . '/chat/completions?api-version=' . $this->apiVersion;

            $headers = [
                'Content-Type: application/json',
                'api-key: ' . $this->apiKey
            ];

            $data = [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Génère une description courte et pertinente pour la catégorie de produits suivante : ' . $categoryName . '. La description doit être en français et ne pas dépasser 200 caractères.'
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour les environnements de développement

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                error_log('OpenAI API error: HTTP ' . $httpCode . ' - ' . $response);
                return 'Description générée automatiquement pour la catégorie ' . $categoryName;
            }

            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                return trim($result['choices'][0]['message']['content']);
            } else {
                error_log('OpenAI API unexpected response: ' . $response);
                return 'Description générée automatiquement pour la catégorie ' . $categoryName;
            }
        } catch (Exception $e) {
            error_log('Error generating description: ' . $e->getMessage());
            return 'Description générée automatiquement pour la catégorie ' . $categoryName;
        }
    }
}
?>