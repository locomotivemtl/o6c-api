<?php

declare(strict_types=1);

namespace Only6\Services;

use PDO;

class UserRepository
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $username
     * @param string $password
     * @return string|null
     */
    public function login(string $username, string $password): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT `id`,`password` FROM `users` WHERE `username`=:username AND `active`=1 LIMIT 1'
        );
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password']) === true) {
            return $user['id'];
        }
        return null;
    }
}
