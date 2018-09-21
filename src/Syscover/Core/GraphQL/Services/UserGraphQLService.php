<?php namespace Syscover\Core\GraphQL\Services;

use Illuminate\Support\Facades\Auth;

class UserGraphQLService
{
    public function resolveUser($root, array $args)
    {
        return Auth::guard($args['guard'] ?? null)->user();
    }
}