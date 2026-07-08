<?php
/**
 * User Model
 * @package MyPortfolioPro\Models
 */

namespace App\Models;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'username',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'is_active',
    ];

    public function getByUsername(string $username): bool|array
    {
        return $this->findBy('username', $username);
    }

    public function getByEmail(string $email): bool|array
    {
        return $this->findBy('email', $email);
    }

    public function getUserPortfolios(int $userId): bool|array
    {
        return $this->db->findAll('portfolios', ['user_id' => $userId]);
    }
}
