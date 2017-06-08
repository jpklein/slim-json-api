<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.4
 */
declare(strict_types=1);

namespace RestSample\SlimMiddleware;

// Aliases psr-7 objects
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class JsonApiResponsibilitiesMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        return $next($request, $response);
    }
}
