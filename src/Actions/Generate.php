<?php

declare(strict_types=1);

namespace Only6\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response as SlimResponse;
use Only6\Services\LinkRepository;

class Generate
{
    /**
     * @var LinkRepository
     */
    private $linkRepository;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->linkRepository = $container->get(LinkRepository::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $domain = $request->getAttribute('domain');
        $user = $request->getAttribute('user');
        $url = $this->getUrlFromRequest($request);
        if (!$url) {
            return new SlimResponse(400);
        }

        $existingCode = $this->linkRepository->getCodeFromUrl($url, $user, $domain);

        if ($existingCode === null) {
            $code = $this->linkRepository->createLink($url, $user, $domain);
        } else {
            $code = $existingCode;
        }

        $ret = [
            'domain' => $domain,
            'code' => $code,
            'long' => $url,
            'short' => $request->getUri()->getScheme() . '://' . $domain . '/' . $code
        ];

        $response->getBody()->write((string)json_encode($ret));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getUrlFromRequest(Request $request): ?string
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['url'])) {
            return $queryParams['url'];
        }

        $body = json_decode((string)$request->getBody(), true);
        if (isset($body['url'])) {
            return $body['url'];
        }

        return null;
    }
}
