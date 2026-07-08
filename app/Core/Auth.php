<?php
/**
 * Authentication Handler
 * @package MyPortfolioPro\Core
 */

namespace App\Core;

class Auth
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        Session::start();
    }

    public function login(string $username, string $password): bool
    {
        $user = $this->db->find('users', ['username' => $username]);
        
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('email', $user['email']);
        Session::set('first_name', $user['first_name']);
        Session::set('last_name', $user['last_name']);

        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user['id']]);

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function register(string $username, string $email, string $password, string $firstName = '', string $lastName = ''): bool|string
    {
        if ($this->db->find('users', ['username' => $username])) {
            return 'Username already exists';
        }

        if ($this->db->find('users', ['email' => $email])) {
            return 'Email already exists';
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]),
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];

        return $this->db->insert('users', $data) ? true : 'Registration failed';
    }

    public function isAuthenticated(): bool
    {
        return Session::has('user_id');
    }

    public function getUser(): bool|array
    {
        if (!$this->isAuthenticated()) {
            return false;
        }
        return $this->db->find('users', ['id' => Session::get('user_id')]);
    }

    public function getUserId(): mixed
    {
        return Session::get('user_id');
    }

    public function require(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
    }
}
