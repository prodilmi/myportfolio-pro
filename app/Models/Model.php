<?php
/**
 * Base Model Class
 * @package MyPortfolioPro\Models
 */

namespace App\Models;

use App\Core\Database;

class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function create(array $data): bool|int
    {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->insert($this->table, $filtered);
    }

    public function update(int $id, array $data): bool
    {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->update($this->table, $filtered, [$this->primaryKey => $id]);
    }

    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }

    public function find(int $id): bool|array
    {
        return $this->db->find($this->table, [$this->primaryKey => $id]);
    }

    public function findBy(string $key, mixed $value): bool|array
    {
        return $this->db->find($this->table, [$key => $value]);
    }

    public function all(int $limit = 0, int $offset = 0): bool|array
    {
        return $this->db->findAll($this->table, [], ['*'], $limit, $offset);
    }

    public function where(array $conditions, int $limit = 0, int $offset = 0): bool|array
    {
        return $this->db->findAll($this->table, $conditions, ['*'], $limit, $offset);
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}
