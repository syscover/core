<?php namespace Syscover\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\DB;

/**
 * Class Model
 * @package Syscover\Pulsar\Core
 */

class CoreModel extends BaseModel
{
    protected $table;

    /**
     * Overwrite construct to set params in model
     *
     * Model constructor.
     * @param array $attributes
     */
    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param   $query
     * @return  mixed
     */
    public function scopeBuilder($query)
    {
        return $query;
    }

    /**
     * Add SQL_CALC_FOUND_ROWS to statement
     *
     * @param   $query
     * @return  mixed
     */
    public function scopeCalculateFoundRows($query)
    {
        return $query->select(DB::raw('SQL_CALC_FOUND_ROWS *'));
    }

    /**
     * Filter query with parameters passe
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterQuery($query, $filters)
    {
        // apply filters
        if(is_array($filters) && count($filters) > 0)
        {
            foreach ($filters as $column => $value)
                $query->where($column, $value);
        }

        return $query;
    }

    /**
     * Get columns name from table
     * @return array
     */
    public function getTableColumns() {
        return DB::getSchemaBuilder()
            ->getColumnListing($this->table);
    }

    /**
     * @param   $id
     * @param   $langId
     * @param   bool $deleteLangDataRecord
     * @param   array $filters  filters to select and delete records
     * @return	void
     */
    public static function deleteTranslationRecord($id, $langId, $deleteLangDataRecord = true, $filters = [])
    {
        $instance = new static;

        $instance::where('id', $id)
            ->where('lang_id', $langId)
            ->filterQuery($filters)
            ->delete();

        if($deleteLangDataRecord)
            $instance::deleteDataLang($langId, $id);
    }

    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   string  $langId
     * @param   int     $id
     * @param   array   $filters            filters to select and updates records
     * @return	string
     */
    public static function addDataLang(
        $langId,
        $id = null,
        $filters = []
    )
    {
        // if id is equal to null, is a new object
        if($id === null)
        {
            $json[] = $langId;
        }
        else
        {
            $instance   = new static;

            // get the first record, record previous to recent record
            $object = $instance::where('id', $id)
                ->filterQuery($filters)
                ->first();

            if($object !== null)
            {
                // get data_lang from object, check that has array in data_lang column
                $json = is_array($object->data_lang)? $object->data_lang : [];

                // add new language
                $json[] = $langId;

                // updates all objects with new language variables
                $instance::where($object->table . '.id', $object->id)
                    ->filterQuery($filters)
                    ->update([
                        'data_lang' => json_encode($json)
                    ]);
            }
            else
            {
                $json[] = $langId;
            }
        }

        return $json;
    }

    /**
     * Function to delete lang record from json field
     *
     * @param   string  $langId
     * @param   int     $id
     * @param   string  $dataLangModelId  id column from table thar contain data_lang column, may be ix or id like product table
     */
    public static function deleteDataLang(
        $langId,
        $id,
        $dataLangModelId = 'id'
    )
    {
        $instance   = new static;
        $object     = $instance::where($dataLangModelId, $id)->first();

        if($object != null)
        {
            $json = $object->data_lang;

            // unset isn't correct, get error to reorder array
            $langArray = [];
            foreach($json as $jsonLang)
            {
                if($jsonLang != $langId)
                {
                    $langArray[] = $jsonLang;
                }
            }

            $instance::where($object->table . '.' . $dataLangModelId, $id)
                ->update([
                    'data_lang'  => json_encode($langArray)
                ]);
        }
    }
}