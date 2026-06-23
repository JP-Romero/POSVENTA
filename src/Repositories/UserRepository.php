<?php

namespace App\Repositories;

class UserRepository extends Repository
{
    protected string $table = 'usuarios';

    public function findByUsername(string $username): ?object
    {
        $this->db->query('SELECT * FROM usuarios WHERE usuario = :username');
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function findByEmail(string $email): ?object
    {
        $this->db->query('SELECT * FROM usuarios WHERE correo = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function findWithRole(int $id): ?object
    {
        $this->db->query('SELECT u.*, r.nombre as rol_nombre
                          FROM usuarios u
                          INNER JOIN roles r ON u.id_rol = r.id
                          WHERE u.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findAllWithRoles(): array
    {
        $this->db->query('SELECT u.*, r.nombre as rol_nombre
                          FROM usuarios u
                          INNER JOIN roles r ON u.id_rol = r.id');
        return $this->db->resultSet();
    }

    public function toggleStatus(int $id): bool
    {
        $this->db->query('UPDATE usuarios SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $this->db->query('SELECT id FROM usuarios WHERE usuario = :username AND id != :id');
            $this->db->bind(':username', $username);
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query('SELECT id FROM usuarios WHERE usuario = :username');
            $this->db->bind(':username', $username);
        }
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}
