<?php
declare(strict_types=1);

class AiTraitementController {
    private string $endpoint;
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->endpoint = 'https://api.groq.com/openai/v1/chat/completions';
        $this->apiKey = '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';
        $this->model = 'llama-3.1-8b-instant';
    }

    /**
     * Suggère le dosage, la durée et les effets secondaires pour un traitement donné.
     * @param string $nom Nom du traitement
     * @param string $type Type du traitement
     * @return array|null Un tableau avec 'dosage', 'duree', 'effets_secondaires' ou null si échec
     */
    public function suggererDetailsTraitement(string $nom, string $type): ?array {
        $systemPrompt = "Tu es un assistant médical intelligent. Ton but est de fournir le dosage typique, la durée habituelle, et les effets secondaires fréquents pour le traitement décrit par l'utilisateur. "
                      . "Tu DOIS retourner UNIQUEMENT un objet JSON valide avec les propriétés suivantes : "
                      . "'dosage', 'duree' et 'effets_secondaires'. "
                      . "ATTENTION, le format est TRÈS STRICT : "
                      . "- 'dosage' DOIT IMPÉRATIVEMENT se terminer par 'mg' sans espace avant, et commencer par un nombre (ex: '10mg', '500mg'). "
                      . "- 'duree' DOIT IMPÉRATIVEMENT commencer par un nombre suivi d'un espace et de texte (ex: '7 jours', '1 mois'). "
                      . "N'ajoute AUCUN autre texte ou formatage markdown autour du JSON.";

        $userPrompt = "Traitement: $nom\nType: $type\nQuels sont le dosage, la durée et les effets secondaires typiques ?";

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.2,
            'max_tokens' => 200,
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
                if (isset($json['dosage']) && isset($json['duree']) && isset($json['effets_secondaires'])) {
                    $effets = $json['effets_secondaires'];
                    if (is_array($effets)) {
                        $effetsStr = implode(', ', $effets);
                    } else {
                        $effetsStr = (string) $effets;
                    }
                    return [
                        'dosage' => (string) $json['dosage'],
                        'duree' => (string) $json['duree'],
                        'effets_secondaires' => $effetsStr
                    ];
                }
            }
        }

        return null;
    }
}
?>
