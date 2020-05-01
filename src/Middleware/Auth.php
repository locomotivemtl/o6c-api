<?php

declare(strict_types=1);

namespace Only6\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Lcobucci\JWT\{
    Parser,
    Signer,
    Token,
    ValidationData
};

class Auth implements Middleware
{
    /**
     * @var array<string, string|int>
     */
    private $jwtConfig;
    /**
     * @var Parser
     */
    private $tokenParser;

    /**
     * @var Signer
     */
    private $tokenSigner;

    /**
     * @var ValidationData
     */
    private $validationData;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->jwtConfig = $container->get('config')['jwt'];
        $this->tokenParser = $container->get(Parser::class);
        $this->tokenSigner = $container->get(Signer::class);
        $this->validationData = $container->get(ValidationData::class);
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $this->getTokenFromRequest($request);
        if ($token === null) {
            return new SlimResponse(401);
        }
        if (!$token->getClaim('uid')) {
            return new SlimResponse(401);
        }

        $request = $request->withAttribute('user', $token->getClaim('uid'));
        return $handler->handle($request);
    }

    /**
     * Retrieve, validate and verify the JWT token from the Authorization header.
     *
     * @param Request $request
     * @return Token|null
     */
    private function getTokenFromRequest(Request $request): ?Token
    {
        $headers = $request->getHeaders();
        if (!isset($headers['Authorization'])) {
            return null;
        }
        $bearer = str_replace('Bearer ', '', $headers['Authorization'][0]);

        $token = $this->tokenParser->parse($bearer);

        if ($token->validate($this->validationData) !== true) {
            return null;
        }

        if ($token->verify($this->tokenSigner, (string)$this->jwtConfig['publicKey']) !== true) {
            return null;
        }

        return $token;
    }
}
