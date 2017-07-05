<?php namespace Syscover\Core\GraphQL;

use GraphQL;

class CoreGraphQLServiceProvider
{
    public static function bootGraphQLTypes()
    {
        // BOOTSTRAP CONFIG
        GraphQL::addType(\Syscover\Core\GraphQL\Types\BootstrapConfigType::class, 'CoreBootstrapConfigType');

        // CONFIG
        GraphQL::addType(\Syscover\Core\GraphQL\Interfaces\ConfigInterface::class, 'CoreConfigInterface');
        GraphQL::addType(\Syscover\Core\GraphQL\Types\ConfigOptionType::class, 'CoreConfigOptionType');
        GraphQL::addType(\Syscover\Core\GraphQL\Inputs\ConfigInput::class, 'CoreConfigInput');

        // OBJECT
        GraphQL::addType(\Syscover\Core\GraphQL\Types\ObjectPaginationType::class, 'CoreObjectPagination');
        GraphQL::addType(\Syscover\Core\GraphQL\Interfaces\ObjectInterface::class, 'CoreObjectInterface');

        // SQL INPUT
        GraphQL::addType(\Syscover\Core\GraphQL\Inputs\SQLQueryInput::class, 'CoreSQLQueryInput');

        // TRANSLATION FIELD TYPE
        GraphQL::addType(\Syscover\Core\GraphQL\Types\TranslationFieldType::class, 'CoreTranslationField');
    }

    public static function bootGraphQLSchema()
    {
        GraphQL::addSchema('default', array_merge_recursive(GraphQL::getSchemas()['default'], [
            'query' => [
                // BOOTSTRAP CONFIG
                'coreBootstrapConfig' => \Syscover\Core\GraphQL\Queries\BootstrapConfigQuery::class,

                // CONFIG
                'coreConfig' => \Syscover\Core\GraphQL\Queries\ConfigQuery::class,
            ],
            'mutation' => [ ]
        ]));
    }
}