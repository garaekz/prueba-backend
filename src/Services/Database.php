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
        $this->connect();
    }

    /**
     * Connect to the database.
     * 
     * @throws \PDOException if there is an error connecting to the database.
     */
    private function connect()
    {
        // Retrieve database configuration from environment variables
        $dbConnection = env('DB_CONNECTION', 'mysql');
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
            if ($dbConnection === 'sqlite' && $db === ':memory:') {
                $this->pdo = new PDO('sqlite::memory:');
            } else {
                $this->pdo = new PDO($dsn, $user, $pass, $options);
            }
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

    /**
     * Rollback the database for testing.
     * 
     * Drops all tables and re-creates them.
     * 
     * @return void
     */
    public function rollback()
    {
        try {
            $this->pdo->exec("DROP TABLE IF EXISTS user;");
            $this->pdo->exec("DROP TABLE IF EXISTS user_comment;");
        } catch (\PDOException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function initializeForTests()
    {
        try {
            $this->rollback();
            $sqlFilePath = __DIR__ . '../../../db/initialize-sqlite-db.sql';
            $query = file_get_contents($sqlFilePath);
            if ($query === false) {
                throw new \Exception("Unable to load SQL file.");
            }
            $this->pdo->exec($query);
        } catch (\PDOException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
