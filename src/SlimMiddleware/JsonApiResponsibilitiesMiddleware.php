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
use RestSample\Exceptions\JsonApiException as Exception;

class JsonApiResponsibilitiesMiddleware
{
    // @todo Refactors exceptions to use JsonApiStatusesTrait::consts?
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Throws exception for requests missing Content-Type
        if (!($headers = $request->getHeaders()) || !isset($headers['Content-Type'])) {
            throw new Exception('Bad Request', 400);
        }

        // Throws exception if JSON API media type has extra parameters
        foreach ($headers['Content-Type'] as $value) {
            if (strpos($value, 'application/vnd.api+json') === 0 && strlen($value) !== 24) {
                throw new Exception('Unsupported Media Type', 415);
            }
        }

        // Throws exception if client only accepts JSON API media type with parameters
        if (isset($headers['Accept'])) {
            $hasValidTerm = null;
            foreach ($headers['Accept'] as $value) {
                foreach (explode(',', $value) as $term) {
                    $term = trim($term);
                    if (strpos($term, 'application/vnd.api+json') === 0) {
                        $hasValidTerm = strlen($term) === 24;
                    }
                }
            }
            if ($hasValidTerm === false) {
                throw new Exception('Not Acceptable', 406);
            }
        }

        // Sets JSON API header in responses
        return $next($request, $response->withHeader('Content-Type', 'application/vnd.api+json'));
    }
}
