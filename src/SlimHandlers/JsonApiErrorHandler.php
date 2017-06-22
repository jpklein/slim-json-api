<?php declare(strict_types=1);

namespace RestSample\SlimHandlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Extends default Slim application error handler
 *
 * Outputs a collection of JSON API objects using the HTTP status code
 * when a user-generated error object is caught.
 */
class JsonApiErrorHandler extends \Slim\Handlers\Error
{
    /**
     * Invokes error handler
     *
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Returns execution to the default handler unless flag is set
        if (get_class($exception) !== 'RestSample\Exceptions\JsonApiException') {
            return parent::__invoke($request, $response, $exception);
        }

        $this->writeToErrorLog($exception);

        $body = [
            'errors' => [
                'detail' => $exception->getMessage()
            ]
        ];

        return $response
            ->withJson($body, $exception->getCode())
            ->withHeader('Content-Type', 'application/vnd.api+json');
    }
}
