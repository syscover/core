<?php namespace Syscover\Core\GraphQL\Queries;

use GraphQL;
use Folklore\GraphQL\Support\Query;
use Syscover\Admin\Models\Lang;
use Syscover\Admin\Models\Package;

class BootstrapConfigQuery extends Query
{
    protected $attributes = [
        'name'          => 'BootstrapConfigQuery',
        'description'   => 'Get bootstrap config for application'
    ];

    public function type()
    {
        return GraphQL::Type('CoreBootstrapConfigType');
    }

    public function resolve($root, $args)
    {
        return [
            'base_lang' => base_lang(),
            'langs'     => Lang::where('active', true)->get(),
            'packages'  => Package::all()
        ];
    }
}