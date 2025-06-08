<?php


class Database
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * Database constructor.
     * Establishes the PDO connection using environment variables.
     *
     * @throws PDOException If the connection fails.
     */
    public function __construct()
    {
        $host = getenv('DB_HOST');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,
        ];

        $this->connection = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Returns the PDO connection.
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}

