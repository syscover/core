<?php namespace Syscover\Core\GraphQL;

use GraphQL;

class CoreGraphQLServiceProvider
{
    public static function bootGraphQLTypes()
    {
        GraphQL::addType(\Syscover\Core\GraphQL\Imputs\SQLQueryInput::class, 'CoreSQLQueryInput');
        GraphQL::addType(\Syscover\Core\GraphQL\Interfaces\ConfigInterface::class, 'CoreConfigInterface');
        GraphQL::addType(\Syscover\Core\GraphQL\Types\ConfigOptionType::class, 'CoreConfigOptionType');
    }

    public static function bootGraphQLSchema()
    {
        GraphQL::addSchema('default', array_merge_recursive(GraphQL::getSchemas()['default'], [
            'query' => [
                // CONFIG
                'coreConfig' => \Syscover\Core\GraphQL\Queries\ConfigQuery::class,
            ],
            'mutation' => []
        ]));
    }
}