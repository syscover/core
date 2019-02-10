<?php namespace Syscover\Core\GraphQL\Execution;

use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use Illuminate\Validation\ValidationException;

/**
 * Handle Exceptions that implement Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions
 * and add extra content from them to the 'extensions' key of the Error that is rendered
 * to the User.
 */
class ExtensionValidationErrorHandler implements ErrorHandler
{
    public static function handle(Error $error, \Closure $next): array
    {
        // If an error was caused by exception thrown in resolver, $error->getPrevious() would contain original exception
        // in this case, we try catch ValidationException
        $underlyingException = $error->getPrevious();

        if ($underlyingException && $underlyingException instanceof ValidationException) {
            // Reconstruct the error, passing in the extensions of the underlying exception
            $error = new Error(
                $error->message,
                $error->nodes,
                $error->getSource(),
                $error->getPositions(),
                $error->getPath(),
                $underlyingException,
                [
                    'validationErrors' => $underlyingException->errors()
                ]
            );
        }

        return $next($error);
    }
}
