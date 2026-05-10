<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

/**
 * Classe de base pour la communication avec l'API Groq Cloud.
 */
abstract class BaseAiController
{
    protected string $endpoint;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->endpoint = config::GROQ_ENDPOINT;
        $this->apiKey   = config::GROQ_API_KEY;
        $this->model    = config::GROQ_MODEL;
    }

    /**
     * Envoie une requête à l'API Groq.
     * @param array $messages Liste des messages (role/content)
     * @param float $temperature
     * @param int $maxTokens
     * @param bool $jsonResponse Si vrai, force le format de réponse JSON
     * @return string|null La réponse de l'IA ou null en cas d'erreur.
     */
    protected function callGroq(array $messages, float $temperature = 0.7, int $maxTokens = 1000, bool $jsonResponse = false): ?string
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        if ($jsonResponse) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response && $httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['choices'][0]['message']['content'])) {
                return $this->extractJson($data['choices'][0]['message']['content']);
            }
        }

        return null;
    }

    /**
     * Nettoie le contenu pour extraire le JSON s'il est entouré de markdown.
     */
    private function extractJson(string $content): string
    {
        $content = trim($content);
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```$/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return $content;
    }
}
