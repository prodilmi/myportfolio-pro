<?php
/**
 * Database Connection Class
 */

namespace App\Core;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct($config) {
        $this->connection = new \mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']
        );
        
        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }
        
        $this->connection->set_charset('utf8mb4');
    }
    
    public static function connect($config) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance->connection;
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        $stmt = $this->connection->prepare($sql);
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();
        
        return $this->connection->insert_id;
    }
    
    public function findOne($table, $where) {
        $conditions = [];
        $values = [];
        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $values[] = $value;
        }
        $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $conditions) . " LIMIT 1";
        
        $stmt = $this->connection->prepare($sql);
        $types = str_repeat('s', count($values));
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function findAll($table, $where = [], $options = []) {
        $conditions = [];
        $values = [];
        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $values[] = $value;
        }
        
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if (isset($options['order_by'])) {
            $sql .= " ORDER BY " . $options['order_by'];
        }
        if (isset($options['limit'])) {
            $sql .= " LIMIT " . $options['limit'];
        }
        
        $stmt = $this->connection->prepare($sql);
        $types = str_repeat('s', count($values));
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    public function update($table, $data, $where) {
        $set = [];
        $values = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
            $values[] = $value;
        }
        
        foreach ($where as $key => $value) {
            $values[] = $value;
        }
        
        $where_conditions = [];
        foreach ($where as $key => $value) {
            $where_conditions[] = "$key = ?";
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where_conditions);
        
        $stmt = $this->connection->prepare($sql);
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    public function delete($table, $where) {
        $conditions = [];
        $values = [];
        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $values[] = $value;
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $conditions);
        
        $stmt = $this->connection->prepare($sql);
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
}
