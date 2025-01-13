<?php
/**
 * Fichier Model.php
 *
 * Ce fichier contient la classe `Model`, qui représente une classe générique
 * pour la gestion des modèles dans l'application. Cette classe fournit des
 * fonctions de base pour la gestion des données et la connexion à la base
 * de données, permettant d'établir une base commune pour toutes les autres
 * classes de modèle.
 *
 * PHP version 7.4 or later
 *
 * @category Model
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Model
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use PDO;

/**
 * Class Model
 *
 * Classe générique pour les modèles dans l'application. Cette classe
 * fournit des fonctions de base pour interagir avec la base de données,
 * telles que la connexion à la base de données, l'exécution de requêtes,
 * et la gestion des erreurs.
 *
 * @category Model
 * @package  Models
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/Model Documentation de la classe
 * @since    2025-01-07
 */
class Model
{


    /**
     * Instance de la base de données PDO.
     *
     * Cette propriété statique contient l'instance de la base de données
     * gérée par PDO (PHP Data Objects), utilisée pour effectuer des
     * opérations de lecture et d'écriture dans la base de données.
     *
     * @var PDO Instance de la classe PDO pour la gestion de la base de données.
     */
    private static PDO $_database;

    /**
     * Initialise la connexion à la base de données en utilisant PDO.
     *
     * Cette méthode crée une nouvelle instance de PDO pour établir une connexion
     * à la base de données MySQL spécifiée par les constantes 'DB_HOST', 'DB_NAME',
     * 'DB_USER' et 'DB_PASSWORD'. Le mode d'erreur est configuré pour être
     * silencieux, ce qui signifie que les erreurs ne seront pas rapportées par des
     * exceptions mais peuvent toujours être récupérées par des méthodes PDO
     * appropriées.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    private static function _setDatabase() : void
    {
        self::$_database = new PDO(
            'mysql:host=' . DB_HOST . '; dbname=' . DB_NAME,
            DB_USER, DB_PASSWORD
        );
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$_database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Initialise la connexion à la base de données pour le mode visualiseur en
     * utilisant PDO.
     *
     * Cette méthode crée une nouvelle instance de PDO pour établir une connexion
     * à la base de données MySQL spécifiée par les constantes 'DB_HOST_VIEWER',
     * 'DB_NAME_VIEWER', 'DB_USER_VIEWER' et 'DB_PASSWORD_VIEWER'. Le mode d'erreur
     * est configuré pour être silencieux, ce qui signifie que les erreurs ne seront
     * pas rapportées par des exceptions, mais peuvent être récupérées par les
     * méthodes appropriées de PDO.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    private static function _setDatabaseViewer() : void
    {
        self::$_database = new PDO(
            'mysql:host=' . DB_HOST_VIEWER . '; dbname='
            . DB_NAME_VIEWER, DB_USER_VIEWER, DB_PASSWORD_VIEWER
        );
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$_database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Récupère une instance de la connexion à la base de données.
     *
     * Cette méthode initialise la connexion à la base de données en appelant la
     * méthode 'setDatabase()', puis retourne l'instance de PDO de la base de
     * données.
     * Cela permet d'accéder à la base de données pour exécuter des requêtes SQL.
     *
     * @return PDO L'instance de connexion à la base de données.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    protected function getDatabase() : PDO
    {
        self::_setDatabase();
        return self::$_database;
    }

    /**
     * Récupère une instance de la connexion à la base de données pour les
     * utilisateurs.
     *
     * Cette méthode initialise la connexion à la base de données en appelant la
     * méthode 'setDatabaseViewer()', puis retourne l'instance de PDO de la base de
     * données.
     * Cette connexion est généralement utilisée pour les opérations de lecture et
     * d'affichage des données accessibles aux utilisateurs.
     *
     * @return PDO L'instance de connexion à la base de données pour les
     * utilisateurs.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    protected function getDatabaseViewer() : PDO
    {
        self::_setDatabaseViewer();
        return self::$_database;
    }
}
