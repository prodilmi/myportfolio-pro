<?php
/**
 * Database Connection Handler
 * @package MyPortfolioPro\Core
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public static function getInstance(array $config = []): Database
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    private function connect(): void
    {
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['driver'],
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): bool|array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function insert(string $table, array $data): bool|int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            '`' . implode('`, `', $columns) . '`',
            implode(', ', $placeholders)
        );

        if ($this->execute($sql, array_values($data))) {
            return $this->connection->lastInsertId();
        }
        return false;
    }

    public function update(string $table, array $data, array $where): bool
    {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "`{$key}` = ?";
        }

        $conditions = [];
        foreach (array_keys($where) as $key) {
            $conditions[] = "`{$key}` = ?";
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $set),
            implode(' AND ', $conditions)
        );

        return $this->execute($sql, array_merge(array_values($data), array_values($where)));
    }

    public function delete(string $table, array $where): bool
    {
        $conditions = [];
        foreach (array_keys($where) as $key) {
            $conditions[] = "`{$key}` = ?";
        }

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            implode(' AND ', $conditions)
        );

        return $this->execute($sql, array_values($where));
    }

    public function find(string $table, array $where, array $select = ['*']): bool|array
    {
        $conditions = [];
        foreach (array_keys($where) as $key) {
            $conditions[] = "`{$key}` = ?";
        }

        $sql = sprintf(
            'SELECT %s FROM `%s` WHERE %s LIMIT 1',
            implode(', ', $select),
            $table,
            implode(' AND ', $conditions)
        );

        $results = $this->query($sql, array_values($where));
        return $results ? $results[0] : false;
    }

    public function findAll(string $table, array $where = [], array $select = ['*'], int $limit = 0, int $offset = 0): bool|array
    {
        $conditions = [];
        foreach (array_keys($where) as $key) {
            $conditions[] = "`{$key}` = ?";
        }

        $sql = sprintf(
            'SELECT %s FROM `%s` %s',
            implode(', ', $select),
            $table,
            count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : ''
        );

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->query($sql, array_values($where));
    }
}
