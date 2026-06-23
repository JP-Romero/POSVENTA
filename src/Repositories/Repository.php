<?php

namespace App\Repositories;

use App\Core\Database;

abstract class Repository
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findAll(): array
    {
        $this->db->query("SELECT * FROM {$this->table}");
        return $this->db->resultSet();
    }

    public function find(int $id): ?object
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql);
        
        foreach ($data as $key => $value) {
            $this->db->bind(":{$key}", $value);
        }
        
        return $this->db->execute();
    }

    public function update(int $id, array $data): bool
    {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $set);
        
        $sql = "UPDATE {$this->table} SET {$setString} WHERE id = :id";
        $this->db->query($sql);
        
        foreach ($data as $key => $value) {
            $this->db->bind(":{$key}", $value);
        }
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function count(): int
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
