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
     * Get columns name from table
     * @return array
     */
    public function getTableColumns() {
        return DB::getSchemaBuilder()
            ->getColumnListing($this->table);
    }

    /**
     * @param   $objectId
     * @param   $langId
     * @param   bool $deleteLangDataRecord
     * @return	void
     */
    public static function deleteTranslationRecord($objectId, $langId, $deleteLangDataRecord = true)
    {
        $instance = new static;

        $instance::where('object_id', $objectId)
            ->where('lang_id', $langId)
            ->delete();

        if($deleteLangDataRecord)
            $instance::deleteDataLang($langId, $objectId);
    }

    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   string  $langId
     * @param   int     $objectId
     * @param   string  $dataLangModelId  id column from table thar contain data_lang column, may be object_id or id like product table
     * @return	string
     */
    public static function addDataLang(
        $langId,
        $objectId = null,
        $dataLangModelId = 'object_id'
    )
    {
        // if id is equal to null, is a new object
        if($objectId === null)
        {
            $json[] = $langId;
        }
        else
        {
            $instance   = new static;
            $object     = $instance::where($dataLangModelId, $objectId)->first();

            if($object !== null)
            {
                // get data_lang from object, check that has array in data_lang column
                $json = is_array($object->data_lang)? $object->data_lang : [];

                // add new language
                $json[] = $langId;

                // updates all objects with new language variables
                $instance::where($object->table . '.' . $dataLangModelId, $object->{$dataLangModelId})
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
     * @param   int     $objectId
     * @param   string  $dataLangModelId  id column from table thar contain data_lang column, may be object_id or id like product table
     */
    public static function deleteDataLang(
        $langId,
        $objectId,
        $dataLangModelId = 'object_id'
    )
    {
        $instance   = new static;
        $object     = $instance::where($dataLangModelId, $objectId)->first();

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

            $instance::where($object->table . '.' . $dataLangModelId, $objectId)
                ->update([
                    'data_lang'  => json_encode($langArray)
                ]);
        }
    }
}