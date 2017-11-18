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
     * @param   $objId
     * @param   $langId
     * @param   bool $deleteLangDataRecord
     * @return	void
     */
    public static function deleteTranslationRecord($objId, $langId, $deleteLangDataRecord = true)
    {
        $instance = new static;

        $instance::where('obj_id', $objId)
            ->where('lang_id', $langId)
            ->delete();

        if($deleteLangDataRecord)
            $instance::deleteDataLang($langId, $objId);
    }


    /**
     * Manage data_lang column
     */

    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   int $objId
     * @param   string $lang
     * @return	string
     */
    public static function addDataLang($lang, $objId = null)
    {
        // if id is equal to null, is a new object
        if($objId === null)
        {
            $json[] = $lang;
        }
        else
        {
            $instance   = new static;
            $object     = $instance::where('obj_id', $objId)->first();

            if($object != null)
            {
                $json = $object->data_lang; // get data_lang from object
                $json[] = $lang; // add new language

                // updates all objects with new language variables
                $instance::where($object->table . '.obj_id', $object->obj_id)
                    ->update([
                        'data_lang' => json_encode($json)
                    ]);
            }
            else
            {
                $json[] = $lang;
            }
        }

        return $json;
    }

    /**
     * Function to delete lang record from json field
     *
     * @param $objId
     * @param $langId
     */
    public static function deleteDataLang($langId, $objId)
    {
        $instance   = new static;
        $object     = $instance::where('obj_id', $objId)->first();

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

            $instance::where($object->table . '.obj_id', $objId)
                ->update([
                    'data_lang'  => json_encode($langArray)
                ]);
        }
    }
}