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
     * Connect to the database
     */
    private static function setDatabase() {
        self::$database = new PDO('mysql:host=' . DB_HOST . '; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Connect to the database
     */
    private static function setDatabaseViewer() {
        self::$database = new PDO('mysql:host=' . DB_HOST_VIEWER . '; dbname=' . DB_NAME_VIEWER, DB_USER_VIEWER, DB_PASSWORD_VIEWER);
        //self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Return the connection
     *
     * @return PDO
     */
    protected function getDatabase() {
        self::setDatabase();
        return self::$database;
    }

    /**
     * Return the connection
     *
     * @return PDO
     */
    protected function getDatabaseViewer() {
        self::setDatabaseViewer();
        return self::$database;
    }
}
