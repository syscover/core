<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;

class PreferencesQuery extends Query
{
    protected $attributes = [
        'name'          => 'PreferenceQuery',
        'description'   => 'Get preference key/value pair from cache'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::Type('CorePreference'));
    }

    public function args()
    {
        return [
            'keys' => [
                'name'          => 'keys',
                'type'          => Type::listOf(Type::string()),
                'description'   => 'List of keys to retrieve from cache'
            ]
        ];
    }

    public function resolve($root, $args)
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