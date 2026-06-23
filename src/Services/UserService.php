<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Exceptions\ValidationException;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAllWithRoles();
    }

    public function getUserById(int $id): ?object
    {
        return $this->userRepository->findWithRole($id);
    }

    public function createUser(array $data): bool
    {
        // Validate
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }
        
        if (empty($data['usuario'])) {
            $errors['usuario'] = 'El usuario es requerido';
        } elseif ($this->userRepository->usernameExists($data['usuario'])) {
            $errors['usuario'] = 'El usuario ya existe';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es requerida';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['estado'] = $data['estado'] ?? 1;

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        // Validate
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        }
        
        if (empty($data['usuario'])) {
            $errors['usuario'] = 'El usuario es requerido';
        } elseif ($this->userRepository->usernameExists($data['usuario'], $id)) {
            $errors['usuario'] = 'El usuario ya existe';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function toggleUserStatus(int $id): bool
    {
        return $this->userRepository->toggleStatus($id);
    }

    public function authenticate(string $username, string $password): ?object
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
