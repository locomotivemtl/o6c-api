<?php

declare(strict_types=1);

namespace Only6\Services;

use PDO;

class LinkRepository
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
     * @param string $url
     * @param string $user
     * @param string $domain
     * @return string
     */
    public function createLink(string $url, string $user, string $domain): string
    {
        $code = $this->generateUniqueCode(5, $domain);
        $statement = $this->pdo->prepare(
            'INSERT INTO `links` (`id`, `created`, `user`, `code`, `domain`, `url`) VALUES (UUID(), CURRENT_TIMESTAMP(), :user, :code, :domain, :url)'
        );
        $statement->execute(
            [
                'user' => $user,
                'code' => $code,
                'domain' => $domain,
                'url' => $url
            ]
        );
        return $code;
    }

    /**
     * @param int $length
     * @param string $domain
     * @return string
     */
    public function generateUniqueCode(int $length, string $domain): string
    {
        do {
            $code = $this->generateRandomCode($length);
            $statement = $this->pdo->prepare('SELECT `code` FROM `links` WHERE code=:code AND domain=:domain LIMIT 1');
            $statement->bindParam(':code', $code);
            $statement->bindParam(':domain', $domain);
            $statement->execute();
            $exists = !!$statement->fetchColumn(0);
        } while ($exists === true);

        return $code;
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomCode(int $length): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz-_';
        $code = '';
        $count = strlen($chars);
        while ($length--) {
            $code .= $chars[rand(0, $count - 1)];
        }
        return $code;
    }

    /**
     * @param string $code
     * @param string $domain
     * @return string|null
     */
    public function getUrlFromCode(string $code, string $domain): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT `url` FROM `links` WHERE `code`=:code AND `domain`=:domain LIMIT 1'
        );
        $statement->bindParam(':code', $code, PDO::PARAM_STR);
        $statement->bindParam(':domain', $domain, PDO::PARAM_STR);
        $statement->execute();
        $url = $statement->fetchColumn(0);
        return $url ? $url : null;
    }

    /**
     * @param string $url
     * @param string $user
     * @param string $domain
     * @return string|null
     */
    public function getCodeFromUrl(string $url, string $user, string $domain): ?string
    {
        $statement = $this->pdo->prepare(
            'SELECT `code` FROM `links` WHERE `domain`=:domain AND `user`=:user AND `url`=:url LIMIT 1'
        );
        $statement->bindParam(':domain', $domain, PDO::PARAM_STR);
        $statement->bindParam(':user', $user, PDO::PARAM_STR);
        $statement->bindParam(':url', $url, PDO::PARAM_STR);
        $statement->execute();
        $code = $statement->fetchColumn(0);
        return $code ? $code : null;
    }
}
