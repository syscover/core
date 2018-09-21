<?php namespace Syscover\Core\GraphQL\Services;

use Illuminate\Support\Facades\Auth;

class UserGraphQLService
{
    public function resolveUser($root, array $args)
    {
        if(isset($args['guard']))
        {
            return Auth::guard($args['guard'])->user();
        }

        return Auth::user();
    }

    public function resolveCheck($root, array $args)
    {
        if(isset($args['guard']))
        {
            return Auth::guard($args['guard'])->check();
        }

        return Auth::check();
    }
}