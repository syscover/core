<?php namespace Syscover\Core\GraphQL\Services;

class CacheGraphQLService
{
    public function resolvePreferences($root, array $args)
    {
        $response = [];
        foreach ($args['keys'] as $key)
        {
            $response[] = (object)[
                'key'   => $key,
                'value' => cache($key)
            ];
        }
        return $response;
    }
}