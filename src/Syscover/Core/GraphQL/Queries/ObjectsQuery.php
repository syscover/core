<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use Syscover\Core\Services\SQLService;

class ObjectsQuery extends Query
{
    protected $attributes = [
        'name'          => 'ObjectsQuery',
        'description'   => 'Query to get objects list.'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CoreObjectInterface'));
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
        $query = $model->builder();

        if(isset($args['sql']))
        {
            $query = SQLService::getQueryFiltered($query, $args['sql']);
            $query = SQLService::getQueryOrderedAndLimited($query, $args['sql']);
        }

        return $query->get();
    }
}