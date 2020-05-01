<?php

declare(strict_types=1);

namespace Only6\Services;

use PDO;
use Psr\Http\Message\ServerRequestInterface;

class HitRepository
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
     * @param string $code
     * @param ServerRequestInterface $request
     */
    public function createLog(string $code, ServerRequestInterface $request): void
    {
        $ip = $request->getAttribute('client-ip');
        $domain = $request->getAttribute('domain');
        $statement = $this->pdo->prepare(
            'INSERT INTO `hits` (`id`, `ts`,  `code`, `domain`, `ip`) VALUES (UUID(), CURRENT_TIMESTAMP(), :code, :domain, INET_ATON(:ip))'
        );
        $statement->bindParam(':code', $code, PDO::PARAM_STR);
        $statement->bindParam(':domain', $domain, PDO::PARAM_STR);
        $statement->bindParam(':ip', $ip, PDO::PARAM_INT);
        $statement->execute();
    }
}
