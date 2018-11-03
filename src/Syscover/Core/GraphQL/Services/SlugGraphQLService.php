<?php namespace Syscover\Core\GraphQL\Services;

use Syscover\Core\Services\SlugService;

class SlugGraphQLService
{
    public function resolveSlug($root, array $args)
    {
        return SlugService::checkSlug($args['model'], $args['slug'], $args['id'] ?? null, $args['column'] ?? 'slug', $args['lang_id'] ?? null);
    }
}