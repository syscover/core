<?php namespace Syscover\Core\GraphQL\Services;

use Illuminate\Support\Facades\Cache;

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

    public function update($root, array $args)
    {
        $response = [];
        foreach ($args['preferences'] as $preference)
        {
            Cache::forever($preference['key'], $preference['value']);
            $response[] = (object)$preference;
        }

        return $response;
    }
}