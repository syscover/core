<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class TranslationFieldType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'TranslationFieldType',
        'description'   => 'Type to define field that has multiple translations and this is saved in json format in column database, ex. field.labels'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Lang id from language that value is translated'
            ],
            'value' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Value'
            ]
        ];
    }
}