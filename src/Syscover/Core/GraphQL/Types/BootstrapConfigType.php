<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class BootstrapConfigType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'BootstrapConfigType',
        'description'   => 'Config options defined to start application'
    ];

    public function fields()
    {
        return [
            'base_lang' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Base lang of application'
            ],
            'langs' => [
                'type' => Type::listOf(GraphQL::type('AdminLang')),
                'description' => 'List of languages'
            ]
        ];
    }
}