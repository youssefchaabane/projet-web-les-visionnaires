<?php
declare(strict_types=1);

<<<<<<< HEAD
require_once __DIR__ . '/BaseAiController.php';

class AiController extends BaseAiController {

    public function __construct() {
        parent::__construct();
=======
class AiController {
    private string $endpoint;
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->endpoint = 'https://api.groq.com/openai/v1/chat/completions';
        $this->apiKey = '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';
        $this->model = 'llama-3.1-8b-instant';
>>>>>>> 4093244f42fab959fbb4c9060135eeb9f9293817
    }

    /**
     * Suggère un ID de traitement pour une allergie donnée, parmi une liste de traitements.
     * @param array $allergie Données de l'allergie
     * @param array $traitements Liste des traitements disponibles
     * @return int|null L'ID du traitement suggéré, ou null si échec.
     */
    public function suggererTraitement(array $allergie, array $traitements): ?int {
        // Préparation du contenu de l'allergie
        $allergieTexte = "Allergie: " . ($allergie['nom'] ?? '') . "\n";
        $allergieTexte .= "Description: " . ($allergie['description'] ?? '') . "\n";
        $allergieTexte .= "Symptomes: " . ($allergie['symptomes'] ?? '') . "\n";
        $allergieTexte .= "Niveau de danger: " . ($allergie['niveau_danger'] ?? '') . "\n";

        // Préparation de la liste des traitements
        $traitementsTexte = "Liste des traitements disponibles :\n";
        foreach ($traitements as $t) {
            $traitementsTexte .= "- ID: " . ($t['id_traitement'] ?? '') . " | Nom: " . ($t['nom'] ?? '') . " | Type: " . ($t['type_traitement'] ?? '') . " | Effets: " . ($t['effets_secondaires'] ?? '') . "\n";
        }

        $systemPrompt = "Tu es un assistant médical intelligent. Ton seul but est d'analyser l'allergie fournie et la liste des traitements disponibles, puis de recommander le traitement le plus approprié. "
                      . "Tu DOIS retourner UNIQUEMENT un objet JSON valide avec une seule propriété 'id_traitement' contenant l'ID (entier) du traitement choisi. "
                      . "N'ajoute AUCUN autre texte ou formatage markdown autour du JSON. Si aucun traitement ne semble correspondre, retourne 'id_traitement': null.";

        $userPrompt = $allergieTexte . "\n" . $traitementsTexte;

<<<<<<< HEAD
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        $content = $this->callGroq($messages, 0.0, 100, true);

        if ($content) {
            $json = json_decode(trim($content), true);
            if (isset($json['id_traitement']) && is_numeric($json['id_traitement'])) {
                return (int)$json['id_traitement'];
=======
        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.0,
            'max_tokens' => 100,
            'response_format' => ['type' => 'json_object']
        ];

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
                $content = trim($data['choices'][0]['message']['content']);
                $json = json_decode($content, true);
                if (isset($json['id_traitement']) && is_numeric($json['id_traitement'])) {
                    return (int)$json['id_traitement'];
                }
>>>>>>> 4093244f42fab959fbb4c9060135eeb9f9293817
            }
        }

        return null;
    }
}
?>
