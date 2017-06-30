<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use Illuminate\Support\Facades\App;

class ConfigQuery extends Query
{
    protected $attributes = [
        'name'          => 'ConfigQuery',
        'description'   => 'Get config files from laravel'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::Type('CoreConfigInterface'));
    }

    public function args()
    {
        return [
            'config' => [
                'name'          => 'config',
                'type'          => GraphQL::type('CoreConfigInput'),
                'description'   => 'String to access to config'
            ]
        ];
    }

    public function resolve($root, $args)
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