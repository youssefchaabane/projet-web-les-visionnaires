<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseAiController.php';

class ChatbotController extends BaseAiController
{
    public function __construct() {
        parent::__construct();
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

        // Récupérer la liste complète des allergies et des traitements de la plateforme
        $pdo = config::getConnexion();
        
        $stmtAllergies = $pdo->query('SELECT nom, type, niveau_danger, symptomes, description FROM allergie ORDER BY nom ASC');
        $allAllergies = $stmtAllergies ? $stmtAllergies->fetchAll(PDO::FETCH_ASSOC) : [];
        
        $stmtTraitements = $pdo->query('SELECT nom, type_traitement, dosage, duree, effets_secondaires FROM traitement ORDER BY nom ASC');
        $allTraitements = $stmtTraitements ? $stmtTraitements->fetchAll(PDO::FETCH_ASSOC) : [];
        
        // Récupérer les allergies spécifiques déclarées par cet utilisateur
        $stmtUserAllergies = $pdo->prepare('
            SELECT a.nom, a.type, a.niveau_danger, a.symptomes 
            FROM allergie a 
            INNER JOIN utilisateur_allergie ua ON a.id_allergie = ua.id_allergie 
            WHERE ua.id_user = ?
            ORDER BY a.nom ASC
        ');
        $stmtUserAllergies->execute([$id_user]);
        $userAllergies = $stmtUserAllergies->fetchAll(PDO::FETCH_ASSOC);

        $allergiesTxt = "";
        if (empty($allAllergies)) {
            $allergiesTxt = "Aucune allergie enregistrée dans la base de données.";
        } else {
            foreach ($allAllergies as $all) {
                $allergiesTxt .= "- " . $all['nom'] . " (Type: " . $all['type'] . ", Danger: " . $all['niveau_danger'] . "). Symptômes: " . ($all['symptomes'] ?: 'N/A') . ". Description: " . ($all['description'] ?: 'N/A') . "\n";
            }
        }

        $traitementsTxt = "";
        if (empty($allTraitements)) {
            $traitementsTxt = "Aucun traitement enregistré dans la base de données.";
        } else {
            foreach ($allTraitements as $tr) {
                $traitementsTxt .= "- " . $tr['nom'] . " (Type: " . $tr['type_traitement'] . ", Dosage: " . $tr['dosage'] . ", Durée: " . $tr['duree'] . "). Effets secondaires: " . ($tr['effets_secondaires'] ?: 'N/A') . "\n";
            }
        }

        $userAllergiesTxt = "";
        if (empty($userAllergies)) {
            $userAllergiesTxt = "L'utilisateur n'a déclaré aucune allergie pour le moment.";
        } else {
            foreach ($userAllergies as $all) {
                $userAllergiesTxt .= "- " . $all['nom'] . " (Danger: " . $all['niveau_danger'] . ", Symptômes: " . ($all['symptomes'] ?: 'N/A') . ")\n";
            }
        }

        // Prompt Système personnalisé basé sur le profil réel, les allergies et les traitements
        $systemPrompt = "Tu es 'Assistant ECOSAVE Pro', un médecin assistant virtuel, expert en allergies, traitements médicaux, nutrition, bien-être et écologie.\n" .
                        "Tu as accès en temps réel à l'intégralité de la base de données médicale d'ECOSAVE :\n\n" .
                        "--- 🤧 TOUTES LES ALLERGIES DE LA PLATEFORME ---\n" .
                        $allergiesTxt . "\n" .
                        "--- 💊 TOUS LES TRAITEMENTS DE LA PLATEFORME ---\n" .
                        $traitementsTxt . "\n\n" .
                        "Voici les détails de l'utilisateur actuellement connecté, récupérés en temps réel de notre base de données :\n" .
                        "- Nom & Prénom : " . ($userProfile['nom_prenom'] ?: 'Non spécifié') . "\n" .
                        "- Régime alimentaire : " . ($userProfile['regime_alimentaire'] ?: 'Non spécifié') . "\n" .
                        "- Objectif santé : " . ($userProfile['objectif_sante'] ?: 'Non spécifié') . "\n" .
                        "- Objectif éco : " . ($userProfile['objectif_eco'] ?: 'Non spécifié') . "\n" .
                        "- Niveau d'activité physique : " . ($userProfile['niveau_activite'] ?: 'Non spécifié') . "\n" .
                        "--- 🚫 ALLERGIES DÉCLARÉES PAR CET UTILISATEUR ---\n" .
                        $userAllergiesTxt . "\n\n" .
                        "Consignes importantes :\n" .
                        "1. Utilise toujours ces listes complètes d'allergies et de traitements pour répondre de manière ultra-précise, personnalisée et contextuelle.\n" .
                        "2. Si l'utilisateur pose une question sur une allergie ou un traitement (présents ou non dans sa propre liste), utilise les informations de la plateforme ci-dessus.\n" .
                        "3. Si l'utilisateur demande des conseils par rapport à ses propres allergies déclarées, prends en compte spécifiquement sa liste de manière sécurisée.\n" .
                        "4. Sois encourageant, chaleureux, extrêmement professionnel, bienveillant et utilise des émojis.\n" .
                        "5. Formate tes réponses en Markdown pour une lisibilité premium (gras, listes à puces, tableaux si approprié, etc.).";

        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        // Ajouter l'historique à l'appel API
        foreach ($historique as $msg) {
            $role = ($msg['sender'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['message']];
        }

        // 4. Appeler l'API Groq Cloud via la méthode mutualisée
        $reponseTexte = $this->callGroq($messages, 0.7, 1000);

        if ($reponseTexte === null) {
            $reponseTexte = "Je m'excuse, une erreur s'est produite lors de la génération de ma réponse (vérifiez la connexion à l'API Groq).";
        }

        // 5. Sauvegarder la réponse de l'assistant en base de données
        $this->sauvegarderMessage($id_user, 'bot', $reponseTexte);

        return $reponseTexte;
    }
}
