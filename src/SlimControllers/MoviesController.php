<?php declare(strict_types=1);

namespace RestSample\SlimControllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RestSample\Exceptions\JsonApiException as Exception;
use RestSample\PdoModels\MoviesModel as Model;

class MoviesController
{
    use \RestSample\SlimControllerTrait;

    /**
     * Connects controller to model on demand
     *
     * @return RestSample\PdoModels\MoviesModel
     */
    final private function getModel()
    {
        return new Model(self::$db);
    }

    /**
     * @param  Request  $request
     * @param  Response $response
     *
     * @throws JsonApiException
     * @return Slim\Http\Response
     */
    public function get(Request $request, Response $response)
    {
        // Fetches URI parameters
        // NB there's no need sanitize arguments here. FastRoute
        // performs validation using regex pattern matching
        $id = (int) $request->getAttribute('id');

        // Calls model get method
        $result = $this->getModel()->getOneById($id);

        // Formats output
        return $response->withJson(['data' => [$result]]);
    }
}
