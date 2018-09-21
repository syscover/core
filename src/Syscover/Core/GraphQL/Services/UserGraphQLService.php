<?php namespace Syscover\Core\GraphQL\Services;

class UserGraphQLService
{
    public function resolveUser($root, array $args)
    {
        return auth($args['guard'] ?? null)->user();
    }
}