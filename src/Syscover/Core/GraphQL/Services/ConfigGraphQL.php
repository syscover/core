<?php namespace Syscover\Core\GraphQL\Services;

use Illuminate\Support\Facades\App;

class ConfigGraphQL
{
    public function resolveConfig($root, array $args)
    {
        $config = config($args['config']['key']);

        if(isset($args['config']['lang']) && isset($args['config']['property']))
        {
            // set lang
            App::setLocale($args['config']['lang']);
            $property = $args['config']['property'];

            // translate property indicated
            $config = array_map(function($object) use ($property) {
                $object->{$property} = trans($object->{$property});
                return $object;
            }, $config);
        }

        return $config;
    }
}