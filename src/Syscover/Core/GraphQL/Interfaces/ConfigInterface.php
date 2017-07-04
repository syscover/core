<?php namespace Syscover\Core\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\InterfaceType;

class ConfigInterface extends InterfaceType
{
    protected $attributes = [
        'name'          => 'ConfigInterface',
        'description'   => 'Element in config files server'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of config.'
            ]
        ];
    }

    public function resolveType($value)
    {
        if(true) return GraphQL::type('CoreConfigOptionType');
    }
}