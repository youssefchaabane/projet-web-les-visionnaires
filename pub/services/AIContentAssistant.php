<?php
/**
 * ========================================================================
 * Service IA — Assistant de Contenu (Résumé + Suggestion de Réponse)
 * ========================================================================
 * API   : Groq (OpenAI-compatible)
 * Model : openai/gpt-oss-20b
 * 
 * Fonctionnalités :
 *   1. summarizePublication()  → Résumé IA structuré d'une publication
 *   2. suggestResponse()       → Suggestion de réponse professionnelle
 *   3. generateSuggestionAI()  → Alias simplifié pour suggestion
 * ========================================================================
 */
require_once __DIR__ . '/../../config/config.php';

class AIContentAssistant
{
    // ─── CONFIGURATION GROQ API ───────────────────────────────────────
    private const API_KEY  = config::GROQ_API_KEY;
    private const API_URL  = config::GROQ_ENDPOINT;
    private const MODEL    = config::GROQ_MODEL;
    private const TIMEOUT  = 60;

    // ═══════════════════════════════════════════════════════════════════
    //  1. RÉSUMÉ IA — Analyse structurée d'une publication
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Génère un résumé IA d'une publication et ses commentaires
     * @param  array $pub      Publication ['titre','contenu','date_publication']
     * @param  array $comments Commentaires [['contenu','note','likes_count'],...]
     * @return array ['success'=>bool, 'data'=>string|null, 'error'=>string|null]
     */
    public static function summarizePublication(array $pub, array $comments): array
    {
        error_log("═══════════════════════════════════════════════════");
        error_log("AI ASSISTANT — RÉSUMÉ IA START");
        error_log("Publication: " . ($pub['titre'] ?? 'Sans titre'));
        error_log("Commentaires: " . count($comments));

        // Construire le contexte des commentaires
        $commentContext = '';
        foreach ($comments as $i => $c) {
            $note = isset($c['note']) && $c['note'] !== null ? " (Note: {$c['note']}/5)" : '';
            $likes = isset($c['likes_count']) ? " [❤ {$c['likes_count']}]" : '';
            $contenu = $c['contenu'] ?? $c['contenu_commentaire'] ?? '';
            $commentContext .= "- Commentaire " . ($i + 1) . $note . $likes . ": " . $contenu . "\n";
        }
        if (empty($comments)) {
            $commentContext = "Aucun commentaire.\n";
        }

        $prompt = "Tu es un assistant IA professionnel pour une plateforme de gestion de publications.\n" .
                  "Analyse cette publication et ses commentaires, puis génère un résumé structuré.\n\n" .
                  "PUBLICATION :\n" .
                  "Titre : " . ($pub['titre'] ?? '') . "\n" .
                  "Contenu : " . ($pub['contenu'] ?? $pub['contenu_publication'] ?? '') . "\n" .
                  "Date : " . ($pub['date_publication'] ?? '') . "\n\n" .
                  "COMMENTAIRES (" . count($comments) . ") :\n" . $commentContext . "\n" .
                  "INSTRUCTIONS :\n" .
                  "1. Résume le sujet principal en 2-3 phrases\n" .
                  "2. Synthétise les retours des commentaires\n" .
                  "3. Indique la note moyenne si disponible\n" .
                  "4. Évalue l'engagement global\n\n" .
                  "Réponds en français avec des émojis pour structurer visuellement.";

        $result = self::callAPI($prompt);

        error_log("AI ASSISTANT — RÉSUMÉ RESULT: " . ($result['success'] ? 'OK' : 'FAIL'));
        error_log("═══════════════════════════════════════════════════");

        return $result;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  2. SUGGESTION DE RÉPONSE IA — Réponse professionnelle
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Génère une suggestion de réponse professionnelle
     * @param  array $pub      Publication data
     * @param  array $comments Comments data
     * @return array ['success'=>bool, 'data'=>string|null, 'error'=>string|null]
     */
    public static function suggestResponse(array $pub, array $comments): array
    {
        error_log("═══════════════════════════════════════════════════");
        error_log("AI ASSISTANT — SUGGESTION IA START");
        error_log("Publication: " . ($pub['titre'] ?? 'Sans titre'));

        // Construire le contexte des commentaires
        $commentContext = '';
        foreach ($comments as $i => $c) {
            $note = isset($c['note']) && $c['note'] !== null ? " (Note: {$c['note']}/5)" : '';
            $contenu = $c['contenu'] ?? $c['contenu_commentaire'] ?? '';
            $commentContext .= "- Commentaire " . ($i + 1) . $note . ": " . $contenu . "\n";
        }
        if (empty($comments)) {
            $commentContext = "Aucun commentaire.\n";
        }

        $prompt = "Tu es un community manager professionnel et bienveillant.\n" .
                  "Lis cette publication et ses commentaires, puis suggère UNE réponse professionnelle.\n\n" .
                  "PUBLICATION :\n" .
                  "Titre : " . ($pub['titre'] ?? '') . "\n" .
                  "Contenu : " . ($pub['contenu'] ?? $pub['contenu_publication'] ?? '') . "\n\n" .
                  "COMMENTAIRES :\n" . $commentContext . "\n" .
                  "INSTRUCTIONS :\n" .
                  "1. La réponse doit être empathique et constructive\n" .
                  "2. Adaptée au ton de la publication et des commentaires\n" .
                  "3. En français, entre 3 et 6 phrases\n" .
                  "4. Commence par une formule de politesse\n" .
                  "5. Termine par une ouverture positive\n\n" .
                  "Rédige directement la réponse (pas de titre, pas d'explication) :";

        $result = self::callAPI($prompt);

        error_log("AI ASSISTANT — SUGGESTION RESULT: " . ($result['success'] ? 'OK' : 'FAIL'));
        error_log("═══════════════════════════════════════════════════");

        return $result;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  3. ALIAS SIMPLIFIÉ — Pour usage direct avec texte brut
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Génère une suggestion de réponse à partir d'un texte brut
     * @param  string $text Le texte de la publication
     * @return array  ['success'=>bool, 'data'=>string|null, 'error'=>string|null]
     */
    public static function generateSuggestionAI(string $text): array
    {
        $prompt = "Tu es un assistant professionnel pour un administrateur.\n" .
                  "Lis cette publication et propose une réponse courte, polie et utile en français.\n" .
                  "La réponse doit être claire, respectueuse et adaptée au contenu.\n\n" .
                  "Publication : \"" . $text . "\"\n\n" .
                  "Rédige directement la réponse (3-5 phrases, commence par une formule de politesse) :";

        return self::callAPI($prompt);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  API CALL — Communication avec Groq (OpenAI-compatible)
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Envoie un prompt à l'API Groq et retourne la réponse
     * @param  string $prompt Le prompt à envoyer
     * @return array  ['success'=>bool, 'data'=>string|null, 'error'=>string|null]
     */
    private static function callAPI(string $prompt): array
    {
        try {
            // Vérifier la clé API
            if (empty(self::API_KEY)) {
                error_log("AI ASSISTANT ERROR: Clé API non configurée");
                return ['success' => false, 'data' => null, 'error' => 'Clé API non configurée.'];
            }

            // ─── Payload JSON (format OpenAI) ─────────────────────────
            $postData = [
                'model'       => self::MODEL,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Tu es un assistant IA professionnel. Réponds toujours en français de manière claire et structurée.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens'  => 2048,
                'top_p'       => 0.9,
            ];

            $jsonPayload = json_encode($postData, JSON_UNESCAPED_UNICODE);

            error_log("AI ASSISTANT API URL: " . self::API_URL);
            error_log("AI ASSISTANT MODEL: " . self::MODEL);
            error_log("AI ASSISTANT PAYLOAD SIZE: " . strlen($jsonPayload) . " bytes");

            // ─── Appel cURL ───────────────────────────────────────────
            $ch = curl_init(self::API_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $jsonPayload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . self::API_KEY,
                ],
                CURLOPT_TIMEOUT        => self::TIMEOUT,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // ─── Debug Logs ───────────────────────────────────────────
            error_log("AI ASSISTANT HTTP CODE: " . $httpCode);

            if ($curlError) {
                error_log("AI ASSISTANT CURL ERROR: " . $curlError);
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Impossible de générer une suggestion IA pour le moment. (Erreur connexion)',
                ];
            }

            if ($httpCode !== 200 || !$response) {
                error_log("AI ASSISTANT HTTP ERROR: " . $httpCode);
                error_log("AI ASSISTANT RESPONSE: " . mb_substr($response, 0, 500));
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Impossible de générer une suggestion IA pour le moment. (HTTP ' . $httpCode . ')',
                ];
            }

            // ─── Parse de la réponse ──────────────────────────────────
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("AI ASSISTANT JSON ERROR: " . json_last_error_msg());
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Impossible de générer une suggestion IA pour le moment. (Format invalide)',
                ];
            }

            // ─── Extraire le texte de la réponse ──────────────────────
            if (!isset($data['choices'][0]['message']['content'])) {
                error_log("AI ASSISTANT ERROR: Structure de réponse inattendue");
                error_log("AI ASSISTANT RAW: " . mb_substr($response, 0, 500));
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Impossible de générer une suggestion IA pour le moment. (Réponse vide)',
                ];
            }

            $result = trim($data['choices'][0]['message']['content']);
            error_log("AI ASSISTANT RESULT LENGTH: " . mb_strlen($result) . " chars");
            error_log("AI ASSISTANT RESULT PREVIEW: " . mb_substr($result, 0, 100) . "...");

            if (empty($result)) {
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Impossible de générer une suggestion IA pour le moment. (Réponse vide)',
                ];
            }

            return [
                'success' => true,
                'data'    => $result,
                'error'   => null,
            ];

        } catch (Exception $e) {
            error_log("AI ASSISTANT EXCEPTION: " . $e->getMessage());
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Impossible de générer une suggestion IA pour le moment.',
            ];
        }
    }
}
