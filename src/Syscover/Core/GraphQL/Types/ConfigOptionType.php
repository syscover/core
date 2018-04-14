<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class ConfigOptionType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'ConfigOptionType',
        'description'   => 'Options defined in app/config files'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of config'
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of config'
            ]
        ];
    }

    public function interfaces()
    {
        return [
            GraphQL::type('CoreConfigInterface')
        ];
    }
}