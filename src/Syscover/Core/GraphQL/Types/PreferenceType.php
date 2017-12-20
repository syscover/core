<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;
use Syscover\Core\GraphQL\ScalarTypes\AnyType;

class PreferenceType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'PreferenceType',
        'description'   => 'Object that contain key/value to represent preferences of applications'
    ];

    public function fields()
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The key of preference'
            ],
            'value' => [
                'type' => app(AnyType::class),
                'description' => 'Preference value'
            ]
        ];
    }
}