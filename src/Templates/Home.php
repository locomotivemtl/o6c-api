<?php

declare(strict_types=1);

namespace Only6\Templates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Home
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write((string)file_get_contents(__DIR__ . '/../../views/' . $request->getAttribute('domain') . '/home.html'));
        return $response;
    }
}
