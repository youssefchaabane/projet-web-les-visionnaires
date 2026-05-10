<?php
/**
 * ========================================================================
 * Service IA — Détection de Contenu Inapproprié (AIContentFilter)
 * ========================================================================
 * Pipeline intelligent en 3 étapes :
 *   1. normalizeText()       → normalisation avancée du texte
 *   2. detectLocalBadWords() → dictionnaire local multilingue
 *   3. detectBadWordsGemini()→ analyse IA contextuelle (Gemini API)
 *
 * Langues supportées : FR, EN, AR, dialecte tunisien/maghrébin, IT, ES, DE
 * Détecte : insultes, vulgarité, haine, toxicité, harcèlement
 * ========================================================================
 */
require_once __DIR__ . '/../../config/config.php';

class AIContentFilter
{
    // ─── CONFIGURATION GROQ ───────────────────────────────────────────
    private const GROQ_API_KEY  = config::GROQ_API_KEY;
    private const GROQ_MODEL    = config::GROQ_MODEL;
    private const GROQ_API_URL  = config::GROQ_ENDPOINT;
    private const GROQ_TIMEOUT  = 15;

    // ─── DICTIONNAIRE LOCAL MULTILINGUE ───────────────────────────────
    private const BAD_WORDS = [
        // Français
        'merde','putain','pute','connard','connasse','encule','enculer','salope',
        'bordel','nique','niquer','ntm','fdp','tg','batard','salaud','foutre',
        'branleur','branleuse','couille','bite','chier','degueulasse','petasse',
        'pouffiasse','abruti','con','chiasse','enfoirer','enfoire',

        // Anglais
        'fuck','shit','bitch','asshole','bastard','dick','pussy','whore','slut',
        'cunt','damn','crap','motherfucker','bullshit','nigger','nigga','retard',
        'fag','faggot','stfu','gtfo','dumbass','jackass',

        // Arabe / Tunisien / Maghrébin
        'zebi','zeb','kol','kahba','qahba','nik','nayek','manyak','manyok',
        'koss','tbon','yatik','zamel','hmar','bhim','kelb','sharmouta','sharmout',
        'ahbal','tfou','khra','tozz','weld','alahwa','yasater','ya','hashouma',

        // Italien
        'cazzo','merda','stronzo','stronza','vaffanculo','minchia','coglione',
        'puttana','troia','fanculo',

        // Espagnol
        'puta','mierda','cabron','joder','cojon','pendejo','verga','chingada',

        // Allemand
        'scheiße','scheisse','arschloch','hurensohn','wichser','fotze',
    ];

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTHODE PRINCIPALE — Point d'entrée unique
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Analyse un texte en 3 étapes (normalisation → dictionnaire → IA)
     * @param  string $text Le texte brut à analyser
     * @return array  ['blocked'=>bool, 'score'=>string, 'reason'=>string, 'method'=>string]
     */
    public static function detectBadWordsAdvanced(string $text): array
    {
        $clean = [
            'blocked' => false,
            'score'   => 'faible',
            'reason'  => '',
            'method'  => 'clean',
        ];

        if (empty(trim($text))) {
            return $clean;
        }

        error_log("═══════════════════════════════════════════════════");
        error_log("GEMINI DEBUG START");
        error_log("INPUT TEXT LENGTH: " . mb_strlen($text) . " chars");
        error_log("INPUT TEXT: " . mb_substr($text, 0, 100) . (mb_strlen($text) > 100 ? '...' : ''));

        // ─── ÉTAPE 1 : Normalisation ──────────────────────────────────
        $normalized = self::normalizeText($text);
        error_log("STEP 1 — Normalized: " . $normalized);

        // ─── ÉTAPE 2 : Dictionnaire local ─────────────────────────────
        $localResult = self::detectLocalBadWords($normalized);
        if ($localResult !== null) {
            error_log("STEP 2 — LOCAL HIT: " . $localResult);
            error_log("RESULT: BLOCKED (local dictionary)");
            error_log("═══════════════════════════════════════════════════");
            return [
                'blocked' => true,
                'score'   => 'eleve',
                'reason'  => 'Mot interdit détecté : ' . $localResult,
                'method'  => 'local',
            ];
        }
        error_log("STEP 2 — Local dictionary: CLEAN");

        // ─── ÉTAPE 3 : Analyse IA Groq ──────────────────────────────
        $groqResult = self::detectBadWordsGroq($text);
        if ($groqResult['toxic']) {
            error_log("STEP 3 — GROQ: TOXIC (score: " . $groqResult['score'] . ")");
            error_log("RESULT: BLOCKED (Groq AI)");
            error_log("═══════════════════════════════════════════════════");
            return [
                'blocked' => true,
                'score'   => $groqResult['score'],
                'reason'  => 'Contenu inapproprié détecté par l\'IA',
                'method'  => 'groq',
            ];
        }

        error_log("STEP 3 — Groq AI: CLEAN");
        error_log("RESULT: ACCEPTED");
        error_log("═══════════════════════════════════════════════════");
        return $clean;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ÉTAPE 1 — Normalisation du texte
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Normalise le texte pour contourner les tentatives d'évasion :
     * - Minuscules
     * - Suppression accents
     * - Remplacement leetspeak (@ → a, 0 → o, 3 → e, etc.)
     * - Suppression séparateurs internes (f.u.c.k → fuck)
     * - Réduction lettres répétées (fuuuuck → fuck)
     * - Nettoyage caractères spéciaux
     */
    public static function normalizeText(string $text): string
    {
        $t = mb_strtolower($text, 'UTF-8');

        // Supprimer les accents
        $accents = [
            'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a',
            'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
            'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
            'ý'=>'y','ÿ'=>'y','ñ'=>'n','ç'=>'c',
        ];
        $t = strtr($t, $accents);

        // Leetspeak → lettres normales
        $leetMap = [
            '@'=>'a', '4'=>'a', '3'=>'e', '1'=>'i', '!'=>'i',
            '0'=>'o', '$'=>'s', '5'=>'s', '7'=>'t', '+'=>'t',
        ];
        $t = strtr($t, $leetMap);

        // Supprimer séparateurs internes (f.u.c.k, f-u-c-k, f_u_c_k)
        $t = preg_replace('/([a-z])[.\-_*#\s]+(?=[a-z])/i', '$1', $t);

        // Réduire les lettres répétées (3+ → 1) : fuuuuck → fuck, zebiiii → zebi
        $t = preg_replace('/(.)\\1{2,}/', '$1', $t);

        // Garder uniquement lettres + espaces
        $t = preg_replace('/[^a-z\s]/', '', $t);

        // Normaliser les espaces
        $t = preg_replace('/\s+/', ' ', $t);

        return trim($t);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ÉTAPE 2 — Détection locale (dictionnaire)
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Recherche de mots interdits dans le texte normalisé
     * Vérifie chaque mot du dictionnaire comme sous-chaîne
     * @return string|null Le mot trouvé ou null si propre
     */
    private static function detectLocalBadWords(string $normalized): ?string
    {
        $words = explode(' ', $normalized);

        foreach (self::BAD_WORDS as $bad) {
            // Vérifier chaque mot individuellement
            foreach ($words as $word) {
                if ($word === $bad) {
                    return $bad;
                }
            }
            // Vérifier aussi comme sous-chaîne (pour les mots collés)
            if (mb_strpos($normalized, $bad) !== false) {
                return $bad;
            }
        }
        return null;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ÉTAPE 3 — Détection IA (Gemini API)
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Envoie le texte à Groq pour une analyse contextuelle
     * Détecte les insultes, le harcèlement, la haine dans TOUTES les langues
     * @return array ['toxic'=>bool, 'score'=>string]
     */
    private static function detectBadWordsGroq(string $text): array
    {
        $fallback = ['toxic' => false, 'score' => 'faible'];

        if (empty(self::GROQ_API_KEY)) {
            error_log("GROQ ERROR: API key missing");
            return $fallback;
        }

        try {
            // ─── Construction du prompt ───────────────────────────────
            $prompt = "Tu es un modérateur de contenu professionnel.\n" .
                      "Analyse le texte suivant et détermine s'il contient du contenu inapproprié.\n\n" .
                      "Critères de détection :\n" .
                      "- Insultes et vulgarité (toutes langues : français, anglais, arabe, tunisien, dialectes maghrébins, italien, espagnol, allemand)\n" .
                      "- Discours de haine ou discrimination\n" .
                      "- Harcèlement ou menaces\n" .
                      "- Contenu sexuellement explicite\n" .
                      "- Mots déguisés avec leetspeak, lettres répétées, ou séparateurs\n\n" .
                      "Réponds UNIQUEMENT par un seul mot :\n" .
                      "OUI = le texte est inapproprié/toxique\n" .
                      "NON = le texte est acceptable\n\n" .
                      "Texte à analyser : \"" . $text . "\"\n\n" .
                      "Réponse (OUI ou NON) :";

            // ─── Payload JSON ─────────────────────────────────────────
            $postData = [
                'model' => self::GROQ_MODEL,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.1,
                'max_tokens' => 10,
            ];

            $jsonPayload = json_encode($postData);

            error_log("GROQ API URL: " . self::GROQ_API_URL);
            error_log("GROQ PAYLOAD SIZE: " . strlen($jsonPayload) . " bytes");

            // ─── Appel cURL ───────────────────────────────────────────
            $ch = curl_init(self::GROQ_API_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $jsonPayload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . self::GROQ_API_KEY
                ],
                CURLOPT_TIMEOUT        => self::GROQ_TIMEOUT,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // ─── Debug Logs ───────────────────────────────────────────
            error_log("GROQ HTTP CODE: " . $httpCode);

            if ($curlError) {
                error_log("GROQ ERROR CURL: " . $curlError);
                return $fallback;
            }

            if ($httpCode !== 200 || !$response) {
                error_log("GROQ ERROR: HTTP " . $httpCode);
                error_log("GROQ RESPONSE BODY: " . mb_substr($response, 0, 300));
                return $fallback;
            }

            // ─── Parse de la réponse ──────────────────────────────────
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("GROQ ERROR JSON: " . json_last_error_msg());
                return $fallback;
            }

            if (!isset($data['choices'][0]['message']['content'])) {
                error_log("GROQ ERROR: Unexpected response structure");
                error_log("GROQ RAW: " . mb_substr($response, 0, 500));
                return $fallback;
            }

            $answer = strtoupper(trim($data['choices'][0]['message']['content']));
            error_log("GROQ AI ANSWER: " . $answer);

            // ─── Interprétation ───────────────────────────────────────
            if (strpos($answer, 'OUI') !== false) {
                return ['toxic' => true, 'score' => 'eleve'];
            }

            return $fallback;

        } catch (Exception $e) {
            error_log("GROQ EXCEPTION: " . $e->getMessage());
            return $fallback;
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MÉTHODE SIMPLIFIÉE — Pour usage rapide
    // ═══════════════════════════════════════════════════════════════════
    /**
     * Vérifie si un texte contient du contenu inapproprié
     * @param  string $text Le texte à vérifier
     * @return bool   true = inapproprié, false = propre
     */
    public static function isInappropriate(string $text): bool
    {
        $result = self::detectBadWordsAdvanced($text);
        return $result['blocked'];
    }

    /**
     * Filtre le texte : retourne le texte ou un message d'erreur
     * @param  string $text Le texte à filtrer
     * @return array  ['ok'=>bool, 'text'=>string, 'error'=>string]
     */
    public static function filterBadWords(string $text): array
    {
        $result = self::detectBadWordsAdvanced($text);

        if ($result['blocked']) {
            return [
                'ok'    => false,
                'text'  => '',
                'error' => $result['reason'],
            ];
        }

        return [
            'ok'    => true,
            'text'  => $text,
            'error' => '',
        ];
    }
}
