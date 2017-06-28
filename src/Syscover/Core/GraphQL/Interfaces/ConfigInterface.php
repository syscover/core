<?php namespace Syscover\Core\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\InterfaceType;

class ConfigInterface extends InterfaceType
{
    // field to documentation
    protected $attributes = [
        'name'          => 'Config',
        'description'   => 'Config interface'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of config.'
            ]
        ];
    }

    public function resolveType($root)
    {
        if(true) return GraphQL::type('CoreConfigOptionType');
    }
}