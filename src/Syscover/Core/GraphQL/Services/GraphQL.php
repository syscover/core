<?php namespace Syscover\Core\GraphQL\Services;

use GraphQL\Error\Error;
use Folklore\GraphQL\Error\ValidationError;

class GraphQL
{
    public static function formatError(Error $e)
    {
        $error = [
            'message'   => $e->getMessage(),
            'code'      => $e->getCode(),
        ];

        if($e->getPrevious() && ! empty($e->getPrevious()->errorInfo))
        {
            $error['errorInfo'] = $e->getPrevious()->errorInfo;
        }

        $locations = $e->getLocations();
        if (! empty($locations))
        {
            $error['locations'] = array_map(function ($loc) {
                return $loc->toArray();
            }, $locations);
        }

        $previous = $e->getPrevious();
        if ($previous && $previous instanceof ValidationError)
        {
            $error['validation'] = $previous->getValidatorMessages();
        }

        return $error;
    }
}
