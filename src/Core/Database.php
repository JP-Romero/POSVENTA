<?php

namespace App\Core;

use PDO;
use PDOException;
use App\Config\Config;

class Database
{
    private ?PDO $connection = null;
    private ?PDOStatement $statement = null;
    private string $error = '';

    public function __construct()
    {
        $config = Config::getInstance();
        
        $host = $config->get('database.host', 'localhost');
        $user = $config->get('database.user', 'root');
        $pass = $config->get('database.pass', '');
        $dbname = $config->get('database.name', 'libreria_db');
        $charset = $config->get('database.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        
        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            if ($config->get('app.debug', false)) {
                die("Database connection error: {$this->error}");
            }
            die("Database connection failed");
        }
    }

    public function query(string $sql): self
    {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    public function bind($param, $value, ?int $type = null): self
    {
        if ($type === null) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }

        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    public function execute(): bool
    {
        return $this->statement->execute();
    }

    public function resultSet(): array
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function single(): ?object
    {
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
