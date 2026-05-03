<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Database.php';

/**
 * Façade historique : délègue à {@see Database} (MVP + PDO).
 */
class config
{
    public static function getConnexion(): PDO
    {
        return Database::getInstance();
    }
}
