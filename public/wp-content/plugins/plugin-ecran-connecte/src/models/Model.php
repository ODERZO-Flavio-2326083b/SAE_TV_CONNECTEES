<?php

namespace Models;

use PDO;

/**
 * Class Model
 *
 * Generic class for Model
 * Contain basic function and connection to the database
 *
 * @package Models
 */
class Model
{

    /**
     * @var PDO
     */
    private static $database;

    /**
     * Initialise la connexion à la base de données en utilisant PDO.
     *
     * Cette méthode crée une nouvelle instance de PDO pour établir une connexion
     * à la base de données MySQL spécifiée par les constantes 'DB_HOST', 'DB_NAME',
     * 'DB_USER' et 'DB_PASSWORD'. Le mode d'erreur est configuré pour être silencieux,
     * ce qui signifie que les erreurs ne seront pas rapportées par des exceptions
     * mais peuvent toujours être récupérées par des méthodes PDO appropriées.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    private static function setDatabase() {
        self::$database = new PDO('mysql:host=' . DB_HOST . '; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Initialise la connexion à la base de données pour le mode visualiseur en utilisant PDO.
     *
     * Cette méthode crée une nouvelle instance de PDO pour établir une connexion
     * à la base de données MySQL spécifiée par les constantes 'DB_HOST_VIEWER',
     * 'DB_NAME_VIEWER', 'DB_USER_VIEWER' et 'DB_PASSWORD_VIEWER'. Le mode d'erreur
     * est configuré pour être silencieux, ce qui signifie que les erreurs ne seront
     * pas rapportées par des exceptions, mais peuvent être récupérées par les
     * méthodes appropriées de PDO.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    private static function setDatabaseViewer() {
        self::$database = new PDO('mysql:host=' . DB_HOST_VIEWER . '; dbname=' . DB_NAME_VIEWER, DB_USER_VIEWER, DB_PASSWORD_VIEWER);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Récupère une instance de la connexion à la base de données.
     *
     * Cette méthode initialise la connexion à la base de données en appelant la méthode
     * 'setDatabase()', puis retourne l'instance de PDO de la base de données.
     * Cela permet d'accéder à la base de données pour exécuter des requêtes SQL.
     *
     * @return PDO L'instance de connexion à la base de données.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    protected function getDatabase() {
        self::setDatabase();
        return self::$database;
    }

    /**
     * Récupère une instance de la connexion à la base de données pour les utilisateurs.
     *
     * Cette méthode initialise la connexion à la base de données en appelant la méthode
     * 'setDatabaseViewer()', puis retourne l'instance de PDO de la base de données.
     * Cette connexion est généralement utilisée pour les opérations de lecture et
     * d'affichage des données accessibles aux utilisateurs.
     *
     * @return PDO L'instance de connexion à la base de données pour les utilisateurs.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    protected function getDatabaseViewer() {
        self::setDatabaseViewer();
        return self::$database;
    }
}
