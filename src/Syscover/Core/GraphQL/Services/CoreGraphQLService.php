<?php namespace Syscover\Core\GraphQL\Services;

use Folklore\GraphQL\Support\Query;
use Syscover\Core\Services\SQLService;

abstract class CoreGraphQLService
{
    protected $model;
    protected $service;
    protected $modelInstance;
    protected $serviceInstance;

    public function __construct()
    {
        $this->modelInstance = new $this->model;
        $this->serviceInstance = new $this->service;
    }

    public function get($root, array $args)
    {
        $query = $this->modelInstance->builder();

        if(isset($args['sql']))
        {
            $query = SQLService::getQueryFiltered($query, $args['sql'], $args['filters'] ?? null);
            $query = SQLService::getQueryOrderedAndLimited($query, $args['sql'], $args['filters'] ?? null);
        }

        return $query->get();
    }

    public function paginate($root, array $args)
    {
        return (Object) [
            'query' => $this->modelInstance->calculateFoundRows()->builder()
        ];
    }

    public function find($root, array $args)
    {
        $query = SQLService::getQueryFiltered($this->modelInstance->builder(), $args['sql'], $args['filters'] ?? null);

        return $query->first();
    }

    public function create($root, array $args)
    {
        $this->serviceInstance->create($args['object']);
    }

    public function update($root, array $args)
    {
        $this->serviceInstance->update($args['object']);
    }

    public function delete($root, array $args)
    {
        $object = SQLService::destroyRecord($args['id'], $this->model);

        return $object;
    }
}