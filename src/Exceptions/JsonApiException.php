<?php
namespace RestSample\Exceptions;

/**
 * This exception is thrown when the application encounters a user-
 * generated exception that must be communicated back to the client.
 *
 * @see RestSample\SlimHandlers\JsonApiErrorHandler
 */
class JsonApiException extends \Exception
{
    // Does not override any inherited methods
}
