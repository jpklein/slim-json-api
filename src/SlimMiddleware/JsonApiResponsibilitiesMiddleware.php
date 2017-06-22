<?php declare(strict_types=1);

namespace RestSample\SlimMiddleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;

/**
 * PSR-7 middleware to enforce JSON API protocol
 */
class JsonApiResponsibilitiesMiddleware
{
    // @todo Refactors exceptions to use JsonApiStatusesTrait::consts?
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Throws exception for requests missing Content-Type
        if (!($headers = $request->getHeaders()) || !isset($headers['HTTP_CONTENT_TYPE'])) {
            throw new Exception('Bad Request', 400);
        }

        // Throws exception if JSON API media type has extra parameters
        foreach ($headers['HTTP_CONTENT_TYPE'] as $value) {
            if (strpos($value, 'application/vnd.api+json') === 0 && strlen($value) !== 24) {
                throw new Exception('Unsupported Media Type', 415);
            }
        }

        // Throws exception if client only accepts JSON API media type with parameters
        if (isset($headers['HTTP_ACCEPT'])) {
            $hasValidTerm = null;
            foreach ($headers['HTTP_ACCEPT'] as $value) {
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
        return $next($request, $response)->withHeader('Content-Type', 'application/vnd.api+json');
    }
}
