<?php namespace Syscover\Core\GraphQL\Types;

use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Syscover\Core\Services\SQLService;
use Syscover\Core\GraphQL\ScalarTypes\ObjectType;

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
            'objects' => [
                'type' => Type::listOf(app(ObjectType::class)),
                'description' => 'List of objects filtered',
                'args' => [
                    'sql' => [
                        'type' => Type::listOf(GraphQL::type('CoreSQLQueryInput')),
                        'description' => 'Field to add SQL operations'
                    ],
                    'filters' => [
                        'type' => Type::listOf(GraphQL::type('CoreSQLQueryInput')),
                        'description' => 'Field to add SQL operations, this argument is used to filter all results, for example in multi-language records'
                    ]
                ]
            ],
            'filtered' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'N records filtered'
            ]
        ];
    }

    public function resolveTotalField($object)
    {
        $total = SQLService::countPaginateTotalRecords($object->query);

        // to count elements, if sql has a groupBy statement, count function always return 1
        // check if total is equal to 1, execute FOUND_ROWS() to guarantee the real result
        // https://github.com/laravel/framework/issues/22883
        // https://github.com/laravel/framework/issues/4306
        return $total === 1 ? DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total'"))[0]->total : $total;
    }

    public function resolveObjectsField($object, $args)
    {
        // get query filtered by sql statement and filterd by filters statement
        $query = SQLService::getQueryFiltered($object->query, $args['sql'] ?? null, $args['filters'] ?? null);

        // get query ordered and limited
        $query = SQLService::getQueryOrderedAndLimited($query, $args['sql'] ?? null);

        // get objects filtered
        $objects = $query->get();

        return $objects;
    }

    public function resolveFilteredField()
    {
        return DB::select(DB::raw("SELECT FOUND_ROWS() AS 'filtered'"))[0]->filtered;
    }
}