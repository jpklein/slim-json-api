<?php declare(strict_types=1);

namespace RestSample\SlimControllers;

// Aliases psr-7 objects
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/** */
class UsermovieratingsController //extends \RestSample\SlimController
{
    public function __invoke(Request $request, Response $response)
    {
        return $response
            ->withStatus(400)
            ->withJson(["errors" => ["detail" => "Bad Request"]]);
    }
}
