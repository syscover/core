<?php namespace Syscover\Core\GraphQL\Services;

use Syscover\Core\Services\SQLService;

abstract class CoreGraphQLService
{
    protected $modelClassName;
    protected $serviceClassName;
    protected $model;
    protected $service;

    public function __construct()
    {
        if (isset($this->modelClassName)) $this->model = new $this->modelClassName;
        if (isset($this->serviceClassName)) $this->service = new $this->serviceClassName;
    }

    public function get($root, array $args)
    {
        $query = $this->model->builder();

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
            'query' => $this->model->calculateFoundRows()->builder()
        ];
    }

    public function find($root, array $args)
    {
        $query = SQLService::getQueryFiltered($this->model->builder(), $args['sql'], $args['filters'] ?? null);

        return $query->first();
    }

    public function create($root, array $args)
    {
        return $this->service->create($args['object']);
    }

    public function update($root, array $args)
    {
        return $this->service->update($args['object']);
    }

    public function delete($root, array $args)
    {
        $object = SQLService::deleteRecord($args['id'], $this->model, $args['lang_id'] ?? null, $args['lang_class'] ?? null);

        return $object;
    }
}