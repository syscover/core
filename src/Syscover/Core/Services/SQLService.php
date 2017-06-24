<?php namespace Syscover\Core\Services;

use Syscover\Core\Exceptions\ParameterNotFoundException;
use Syscover\Core\Exceptions\ParameterValueException;

/**
 * Class SQLService
 * @package Syscover\Core\Services
 */
class SQLService
{
    /**
     * @param   $query
     * @param   $args
     * @return  mixed
     *
     * Get N records after filter the query
     */
    public static function getQueryFiltered($query, $args)
    {
        // filter all data by lang
        if(isset($args['lang']))
        {
            $query
                ->where('lang_id', $args['lang'])
                ->where(function ($query) use ($args) {
                    SQLService::setQueryFilter($query, $args['sql']);
                });
        }
        else
        {
            $query = SQLService::setQueryFilter($query, $args['sql']);
        }

        return $query;
    }

    public static function countPaginateTotalRecords($query, $args)
    {
        // filter all data by lang
        if(isset($args['lang']))
            $query->where('lang_id', $args['lang']);

        return $query->count();
    }

    public static function setQueryFilter($query, $sql)
    {
        // commands without pagination and limit
        foreach ($sql as $sentence)
        {
            if(! isset($sentence['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($sentence));

            if(($sentence['command'] === "where" || $sentence['command'] === "orderBy") && ! isset($sentence['column']))
                throw new ParameterNotFoundException('Parameter column not found in request, please set column parameter in ' . json_encode($sentence));

            if(($sentence['command'] === "where" || $sentence['command'] === "orderBy") && ! isset($sentence['operator']))
                throw new ParameterNotFoundException('Parameter operator not found in request, please set operator parameter in ' . json_encode($sentence));


            switch ($sentence['command']) {
                case 'offset':
                case 'limit':
                case 'orderBy':
                    // commands not accepted
                    break;
                case 'where':
                    $query->where($sentence['column'], $sentence['operator'], $sentence['value']);
                    break;
                case 'orWhere':
                    $query->orWhere($sentence['column'], $sentence['operator'], $sentence['value']);
                    break;
                case 'whereIn':
                    $query->whereIn($sentence['column'], $sentence['value']);
                    break;


                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }
        }

        return $query;
    }

    public static function getQueryOrderedAndLimited($query, $sql)
    {
        // sentences for order query and limited
        foreach ($sql as $sentence)
        {
            if(! isset($sentence['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($sentence));

            if($sentence['command'] !== "orderBy" && ! isset($sentence['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter in: ' . json_encode($sentence));

            switch ($sentence['command']) {
                case 'where':
                case 'orWhere';
                case 'whereIn';
                    // commands not accepted, already
                    // implemented in Syscover\Core\Services\SQLService::setQueryFilter method
                    break;
                case 'orderBy':
                    $query->orderBy($sentence['column'], $sentence['operator']);
                    break;
                case 'offset':
                    $query->offset($sentence['value']);
                    break;
                case 'limit':
                    $query->limit($sentence['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be offset or take');
            }
        }

        return $query;
    }
}