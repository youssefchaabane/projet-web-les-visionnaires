<?php

class config
{
    private static $pdo = null;

<<<<<<< HEAD
    // --- Configuration IA (Groq Cloud) ---
    public const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';
    public const GROQ_API_KEY = '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';
    public const GROQ_MODEL    = 'llama-3.1-8b-instant';

=======
>>>>>>> 4093244f42fab959fbb4c9060135eeb9f9293817
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=gestion_allergies;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
