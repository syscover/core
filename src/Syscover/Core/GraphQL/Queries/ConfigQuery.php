<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;

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
            'key' => [
                'name'          => 'key',
                'type'          => Type::nonNull(Type::string()),
                'description'   => 'String to access to config'
            ]
//            ,
//            'translate' => [
//                'name'          => 'translate',
//                'type'          => Type::string(),
//                'description'   => 'Object that contain arguments to translate config values'
//            ]
        ];
    }

    public function resolve($root, $args)
    {
        $config = config($args['key']);

        return $config;
    }
}