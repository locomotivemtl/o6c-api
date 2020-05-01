<?php

declare(strict_types=1);

namespace Only6\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class Domain implements Middleware
{
    /**
     * @var array<string>
     */
    private $domains;

    /**
     * @param array<string> $domains
     */
    public function __construct(array $domains)
    {
        $this->domains = $domains;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $domain = $this->getDomainFromRequest($request);
        if (in_array($domain, $this->domains)) {
            return $handler->handle($request->withAttribute('domain', $domain));
        }
        return new SlimResponse(400);
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getDomainFromRequest(Request $request): string
    {
        $uri = $request->getUri();
        $domain = $uri->getHost();
        if ($uri->getPort() != 443 || $uri->getPort() != 80) {
            $domain .= ':' . $uri->getPort();
        }
        return $domain;
    }
}
