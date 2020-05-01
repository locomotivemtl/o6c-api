<?php

declare(strict_types=1);

namespace Only6\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Only6\Services\UserRepository;

class Login
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Builder
     */
    private $tokenBuilder;

    /**
     * @var Signer
     */
    private $tokenSigner;

    /**
     * @var array<string,string|int>
     */
    private $jwtConfig;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->userRepository = $container->get(UserRepository::class);
        $this->tokenBuilder = $container->get(Builder::class);
        $this->tokenSigner = $container->get(Signer::class);
        $this->jwtConfig = $container->get('config')['jwt'];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $credentials = $this->getCredentialsFromRequest($request);
        if ($credentials === null) {
            return new SlimResponse(400);
        }
        $user = $this->userRepository->login($credentials['username'], $credentials['password']);
        if ($user === null) {
            return new SlimResponse(401);
        }
        $token = $this->tokenBuilder
            ->withClaim('uid', $user)
            ->getToken($this->tokenSigner, new Key((string)$this->jwtConfig['privateKey']));
        $response->getBody()->write(
            (string)json_encode(
                [
                    'token' => (string)$token
                ]
            )
        );
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @return array<string, string>|null
     */
    private function getCredentialsFromRequest(Request $request): ?array
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['username']) && isset($queryParams['password'])) {
            return [
                'username' => $queryParams['username'],
                'password' => $queryParams['password']
            ];
        }

        $body = json_decode((string)$request->getBody(), true);
        if (isset($body['username']) && isset($body['password'])) {
            return [
                'username' => $body['username'],
                'password' => $body['password']
            ];
        }

        return null;
    }
}
