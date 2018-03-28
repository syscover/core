<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use Syscover\Core\Services\SlugService;

class SlugQuery extends Query
{
    protected $attributes = [
        'name'          => 'SlugQuery',
        'description'   => 'Query to get a slug'
    ];

    public function type()
    {
        return Type::string();
    }

    public function args()
    {
        return [
            'model' => [
                'name'          => 'model',
                'type'          => Type::nonNull(Type::string()),
                'description'   => 'Model to consult the slug'
            ],
            'slug' => [
                'name'          => 'slug',
                'type'          => Type::nonNull(Type::string()),
                'description'   => 'Initial slug to check'
            ],
            'id' => [
                'name'          => 'id',
                'type'          => Type::string(),
                'description'   => 'To avoid compare with a determinate id'
            ],
            'field' => [
                'name'          => 'field',
                'type'          => Type::string(),
                'description'   => 'To check against a field that is not called slug'
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return SlugService::checkSlug($args['model'], $args['slug']);
    }
}