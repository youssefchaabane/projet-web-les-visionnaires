<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

class ChatbotController
{
    private string $endpoint;
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->endpoint = 'https://api.groq.com/openai/v1/chat/completions';
        $this->apiKey = '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';
        $this->model = 'llama-3.1-8b-instant';
    }

    /**
     * Initialise la table des messages de chat si elle n'existe pas
     */
    private function initialiserTable(): void
    {
        $pdo = config::getConnexion();
        $sql = "CREATE TABLE IF NOT EXISTS `chatbot_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `id_user` INT NOT NULL,
            `sender` VARCHAR(10) NOT NULL,
            `message` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);
    }

    /**
     * Sauvegarde un message dans la base de données
     */
    public function sauvegarderMessage(int $id_user, string $sender, string $message): void
    {
        $this->initialiserTable();
        $pdo = config::getConnexion();
        $sql = "INSERT INTO `chatbot_messages` (`id_user`, `sender`, `message`) VALUES (?, ?, ?)";
        $pdo->prepare($sql)->execute([$id_user, $sender, $message]);
    }

    /**
     * Récupère l'historique des derniers messages du chat depuis la base de données
     */
    public function recupererHistorique(int $id_user, int $limite = 10): array
    {
        $this->initialiserTable();
        $pdo = config::getConnexion();
        $sql = "SELECT `sender`, `message` FROM (
                    SELECT `id`, `sender`, `message` 
                    FROM `chatbot_messages` 
                    WHERE `id_user` = ? 
                    ORDER BY `id` DESC 
                    LIMIT ?
                ) sub ORDER BY `id` ASC";
        $st = $pdo->prepare($sql);
        $st->bindValue(1, $id_user, PDO::PARAM_INT);
        $st->bindValue(2, $limite, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime tout l'historique de conversation d'un utilisateur
     */
    public function viderHistorique(int $id_user): void
    {
        $this->initialiserTable();
        $pdo = config::getConnexion();
        $st = $pdo->prepare("DELETE FROM `chatbot_messages` WHERE `id_user` = ?");
        $st->execute([$id_user]);
    }

    /**
     * Génère une réponse via Groq Cloud, gère l'historique de conversation et synchronise avec la base de données
     */
    public function genererReponse(int $id_user, string $messageUtilisateur, array $userProfile): string
    {
        // 1. Sauvegarder le message de l'utilisateur en base de données
        $this->sauvegarderMessage($id_user, 'user', $messageUtilisateur);

        // 2. Récupérer l'historique des derniers messages de la base pour la mémoire de l'IA
        $historique = $this->recupererHistorique($id_user, 10);

        // 3. Préparer les messages pour l'API Groq
        $messages = [];

        // Prompt Système personnalisé basé sur le profil réel de la base de données
        $systemPrompt = "Tu es 'Assistant ECOSAVE Pro', un expert en nutrition, bien-être et écologie. " .
                        "Tu aides l'utilisateur à adopter un mode de vie plus sain et respectueux de la planète. " .
                        "Voici les détails de l'utilisateur actuellement connecté, récupérés en temps réel de notre base de données :\n" .
                        "- Nom & Prénom : " . ($userProfile['nom_prenom'] ?: 'Non spécifié') . "\n" .
                        "- Régime alimentaire : " . ($userProfile['regime_alimentaire'] ?: 'Non spécifié') . "\n" .
                        "- Objectif santé : " . ($userProfile['objectif_sante'] ?: 'Non spécifié') . "\n" .
                        "- Objectif éco : " . ($userProfile['objectif_eco'] ?: 'Non spécifié') . "\n" .
                        "- Niveau d'activité physique : " . ($userProfile['niveau_activite'] ?: 'Non spécifié') . "\n\n" .
                        "Consignes importantes :\n" .
                        "1. Utilise toujours ces informations pour donner des réponses ultra-personnalisées.\n" .
                        "2. Ne sors jamais de ton rôle d'assistant bien-être/écologique.\n" .
                        "3. Sois encourageant, chaleureux et professionnel.\n" .
                        "4. Formate tes réponses en Markdown pour une lisibilité premium (gras, listes à puces, emojis, etc.).";

        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        // Ajouter l'historique à l'appel API
        foreach ($historique as $msg) {
            $role = ($msg['sender'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['message']];
        }

        // 4. Appeler l'API Groq Cloud
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];

        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour éviter les soucis SSL sous Windows/XAMPP

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $reponseTexte = "Désolé, je rencontre des difficultés de connexion à mon serveur Groq Cloud (Erreur cURL : " . $error . ").";
        } else {
            $data = json_decode($response, true);
            if (isset($data['choices'][0]['message']['content'])) {
                $reponseTexte = $data['choices'][0]['message']['content'];
            } else {
                $reponseTexte = "Je m'excuse, une erreur s'est produite lors de la génération de ma réponse.";
                if (isset($data['error']['message'])) {
                    $reponseTexte .= " (Détail: " . $data['error']['message'] . ")";
                }
            }
        }

        // 5. Sauvegarder la réponse de l'assistant en base de données
        $this->sauvegarderMessage($id_user, 'bot', $reponseTexte);

        return $reponseTexte;
    }
}
