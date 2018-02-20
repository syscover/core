<?php namespace Syscover\Core\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;
use Syscover\Core\GraphQL\ScalarTypes\AnyType;

class PreferenceInput extends GraphQLType
{
    protected $attributes = [
        'name'          => 'PreferenceInput',
        'description'   => 'Object that contain key/value to represent preferences of applications'
    ];

    protected $inputObject = true;

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