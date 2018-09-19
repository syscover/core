<?php namespace Syscover\Core\GraphQL\Services;

use Illuminate\Support\Facades\DB;
use Syscover\Core\Services\SQLService;

class PaginationGraphQL
{
    public function resolveTotal($root, array $args)
    {
        $total = SQLService::countPaginateTotalRecords($root->query);

        // to count elements, if sql has a groupBy statement, count function always return 1
        // check if total is equal to 1, execute FOUND_ROWS() to guarantee the real result
        // https://github.com/laravel/framework/issues/22883
        // https://github.com/laravel/framework/issues/4306
        return $total === 1 ? DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total'"))[0]->total : $total;
    }

    public function resolveObjects($root, array $args)
    {
        // save eager loads to load after execute FOUND_ROWS() MySql Function
        // FOUND_ROWS function get total number rows of last query, if model has eagerLoads, after execute the query model,
        // will execute eagerLoads losing the reference os last query to execute FOUND_ROWS() MySql Function
        $eagerLoads = $root->query->getEagerLoads();
        $query      = $root->query->setEagerLoads([]);

        // get query filtered by sql statement and filterd by filters statement
        $query = SQLService::getQueryFiltered($query, $args['sql'] ?? null, $args['filters'] ?? null);

        // get query ordered and limited
        $query = SQLService::getQueryOrderedAndLimited($query, $args['sql'] ?? null);

        // get objects filtered
        $objects = $query->get();

        // execute FOUND_ROWS() MySql Function and save filtered value, to be returned in resolveFilteredField() function
        // this function is executed after resolveObjectsField according to the position of fields marked in the GraphQL query
        $root->filtered = DB::select(DB::raw("SELECT FOUND_ROWS() AS 'filtered'"))[0]->filtered;

        // load eager loads
        $objects->load($eagerLoads);

        return $objects;
    }

    public function resolveFiltered($root, array $args)
    {
        return $root->filtered;
    }
}
