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
        if(property_exists($value, 'values'))
        {
            return GraphQL::type('AdminConfigFieldTypeOption');
        }
        else
        {
            return GraphQL::type('CoreConfigOption');
        }
    }
}