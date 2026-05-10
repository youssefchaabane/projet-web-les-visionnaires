<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseAiController.php';

class AiTraitementController extends BaseAiController {

    public function __construct() {
        parent::__construct();
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

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        $content = $this->callGroq($messages, 0.2, 200, true);

        if ($content) {
            $json = json_decode(trim($content), true);
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

        return null;
    }
}
?>
