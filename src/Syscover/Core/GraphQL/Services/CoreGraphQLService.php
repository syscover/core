<?php namespace Syscover\Core\GraphQL\Services;

use Syscover\Core\Services\SQLService;

abstract class CoreGraphQLService
{
    protected $model;
    protected $service;

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

    // method to replace create method
    public function store($root, array $args)
    {
        return $this->service->store($args['payload']);
    }

    public function create($root, array $args)
    {
        return $this->service->create($args['payload']);
    }

    public function update($root, array $args)
    {
        return $this->service->update($args['payload']);
    }

    public function delete($root, array $args)
    {
        $object = SQLService::deleteRecord($args['id'], get_class($this->model), $args['lang_id'] ?? null, $args['lang_class'] ?? null);

        return $object;
    }
}
