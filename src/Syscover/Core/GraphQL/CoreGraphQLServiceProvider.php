<?php namespace Syscover\Core\GraphQL;

use GraphQL;

class CoreGraphQLServiceProvider
{
    public static function bootGraphQLTypes()
    {
        GraphQL::addType(\Syscover\Core\GraphQL\Imputs\SQLQueryInput::class, 'CoreSQLQueryInput');
    }

    public static function bootGraphQLSchema()
    {
    }
}