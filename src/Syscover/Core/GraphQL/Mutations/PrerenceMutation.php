<?php namespace Syscover\Core\GraphQL\Mutations;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Cache;

class PreferenceMutation extends Mutation
{
    public function type()
    {
        return Type::listOf(GraphQL::type('CorePreference'));
    }
}

class UpdatePreferencesMutation extends PreferenceMutation
{
    protected $attributes = [
        'name'          => 'updatePreference',
        'description'   => 'Update action'
    ];

    public function args()
    {
        return [
            'preferences' => [
                'name' => 'preferences',
                'type' => Type::listOf(GraphQL::type('CorePreferenceInput'))
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $response = [];
        foreach ($args['preferences'] as $preference)
        {
            Cache::forever($preference['key'], $preference['value']);
            $response[] = (object)$preference;
        }

        return $response;
    }
}
