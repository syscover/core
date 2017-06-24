<?php namespace Syscover\Core\GraphQL\Imputs;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class SQLQueryInput extends GraphQLType
{
    protected $inputObject = true;

    // field to documentation
    protected $attributes = [
        'name'          => 'CoreSQLQueryInput',
        'description'   => 'Query SQL'
    ];

    public function fields()
    {
        return [
            'command' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'SQL Command, Where, OrderBy, etc.'
            ],
            'operator' => [
                'type' => Type::string(),
                'description' => 'SQL Operator, <, >, ><, etc.'
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'Value to compare.'
            ],
            'column' => [
                'type' => Type::string(),
                'description' => 'Column over operate.'
            ]
        ];
    }
}