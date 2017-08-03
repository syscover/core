<?php namespace Syscover\Core\GraphQL\Interfaces;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\InterfaceType;
use Syscover\Core\GraphQL\ScalarTypes\AnyType;

class ObjectInterface extends InterfaceType
{
    protected $attributes = [
        'name'          => 'ObjectInterface',
        'description'   => 'Interface to define any element that is in database'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(app(AnyType::class)),
                'description' => 'The id of action'
            ]
        ];
    }

    public function resolveType($object)
    {
        switch (get_class($object))
        {
            // ADMIN
//            case Package::class:
//                return GraphQL::type('AdminPackage');
//                break;
        }
    }
}