<?php namespace Syscover\Core\GraphQL\Types;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;
use Syscover\Core\Services\SQLService;

class ObjectPaginationType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'ObjectPaginationType',
        'description'   => 'Pagination for database objects'
    ];

    public function fields()
    {
        return [
            'total' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The total records'
            ],
            'filtered' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'N records filtered'
            ],
            'objects' => [
                'type' => Type::listOf(GraphQL::type('CoreObjectInterface')),
                'description' => 'List of objects filtered',
                'args' => [
                    'sql' => [
                        'type' => Type::listOf(GraphQL::type('CoreSQLQueryInput')),
                        'description' => 'Field to add SQL operations'
                    ]
                ]
            ]
        ];
    }

    public function resolveObjectsField($object, $args)
    {
        // get query ordered and limited
        $query = SQLService::getQueryOrderedAndLimited($object->query, $args['sql']);

        // get objects filtered
        $objects = $query->get();

        return $objects;
    }
}