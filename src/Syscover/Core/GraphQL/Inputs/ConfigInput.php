<?php namespace Syscover\Core\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class ConfigInput extends GraphQLType
{
    protected $inputObject = true;

    // field to documentation
    protected $attributes = [
        'name'          => 'ConfigInput',
        'description'   => 'Input to request config value'
    ];

    public function fields()
    {
        return [
            'key' => [
                'name'          => 'key',
                'type'          => Type::nonNull(Type::string()),
                'description'   => 'String to access to config'
            ],
            'lang' => [
                'name'          => 'lang',
                'type'          => Type::string(),
                'description'   => 'Lang that will be translated config property'
            ],
            'property' => [
                'name'          => 'property',
                'type'          => Type::string(),
                'description'   => 'Property from object that will be translated'
            ]
        ];
    }
}