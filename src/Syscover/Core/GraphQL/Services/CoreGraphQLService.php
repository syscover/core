<?php namespace Syscover\Core\GraphQL\Services;

use Syscover\Core\Services\SQLService;

abstract class CoreGraphQLService
{
    protected $model;
    protected $service;
    protected $modelInstance;
    protected $serviceInstance;

    public function __construct()
    {
        if (isset($this->model)) $this->modelInstance = new $this->model;
        if (isset($this->service)) $this->serviceInstance = new $this->service;
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
        return $this->serviceInstance->create($args['object']);
    }

    public function update($root, array $args)
    {
        return $this->serviceInstance->update($args['object']);
    }

    public function delete($root, array $args)
    {
        $object = SQLService::deleteRecord($args['id'], $this->model, $args['lang_id'] ?? null, $args['lang_class'] ?? null);

        return $object;
    }
}