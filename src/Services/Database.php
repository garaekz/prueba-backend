<?php

namespace Garaekz\Services;

use PDO;

/**
 * Class Database
 * 
 * Represents a database connection.
 */
class Database
{
    private static $instance = null;
    private $pdo;

    /**
     * Database constructor.
     * 
     * Initializes a new instance of the Database class.
     * Connects to the database using the provided configuration.
     * 
     * @throws \PDOException if there is an error connecting to the database.
     */
    private function __construct()
    {
        // Retrieve database configuration from environment variables
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', 3306);
        $db   = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $charset = 'utf8mb4';

        // Create the DSN string
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $db, $charset);

        // Set PDO options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Create a new PDO instance
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Throw an exception if there is an error connecting to the database
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Get the singleton instance of the Database class.
     * 
     * @return Database The singleton instance of the Database class.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Get the PDO connection object.
     * 
     * @return PDO The PDO connection object.
     */
    public function getConnection()
    {
        return $this->pdo;
    }
}
