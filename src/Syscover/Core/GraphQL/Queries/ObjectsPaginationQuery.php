<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use Syscover\Core\Services\SQLService;

class ObjectsPaginationQuery extends Query
{
    protected $attributes = [
        'name'          => 'ObjectsPaginationQuery',
        'description'   => 'Query to get list of objects'
    ];

    public function type()
    {
        return GraphQL::type('CoreObjectPagination');
    }

    public function args()
    {
        return [
            'model' => [
                'name'          => 'model',
                'type'          => Type::nonNull(Type::string()),
                'description'   => 'Model to get for this query'
            ],
            'sql' => [
                'name'          => 'sql',
                'type'          => Type::listOf(GraphQL::type('CoreSQLQueryInput')),
                'description'   => 'Field to add SQL operations'
            ]
        ];
    }

    public function resolve($root, $args)
    {
        // create model
        $model  = new $args['model'];
        $query = SQLService::getQueryFiltered($model->builder(), $args['sql']);

        // count records filtered
        $filtered = $query->count();

        // N total records
        $total = SQLService::countPaginateTotalRecords($model->builder());

        return (Object) [
            'total'     => $total,
            'filtered'  => $filtered,
            'query'     => $query
        ];
    }
}