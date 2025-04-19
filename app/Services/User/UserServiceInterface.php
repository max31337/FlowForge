<?php

namespace App\Services\User;

use App\Models\User;

interface UserServiceInterface
{
    public function getAllUsers();
    public function getUserById(int $id): ?User;
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): bool;
    public function deleteUser(int $id): bool;
    public function getUserByEmail(string $email): ?User;
}