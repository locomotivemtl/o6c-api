<?php

declare(strict_types=1);

namespace Only6\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response as SlimResponse;
use Only6\Services\HitRepository;
use Only6\Services\LinkRepository;

class Retrieve
{
    /**
     * @var HitRepository
     */
    private $hitRepository;

    /**
     * @var LinkRepository
     */
    private $linkRepository;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->hitRepository = $container->get(HitRepository::class);
        $this->linkRepository = $container->get(LinkRepository::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array<string, string> $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $target = $this->linkRepository->getUrlFromCode($args['code'], $request->getAttribute('domain'));
        if (!$target) {
            return new SlimResponse(404);
        }

        $this->hitRepository->createLog($args['code'], $request);

        return $response
            ->withHeader('Location', $target)
            ->withStatus(302);
    }
}
