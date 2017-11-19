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
     * Manage data_lang column
     */

    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   int $objectId
     * @param   string $lang
     * @return	string
     */
    public static function addDataLang($lang, $objectId = null)
    {
        // if id is equal to null, is a new object
        if($objectId === null)
        {
            $json[] = $lang;
        }
        else
        {
            $instance   = new static;
            $object     = $instance::where('object_id', $objectId)->first();

            if($object != null)
            {
                $json = $object->data_lang; // get data_lang from object
                $json[] = $lang; // add new language

                // updates all objects with new language variables
                $instance::where($object->table . '.object_id', $object->object_id)
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
     * @param $objectId
     * @param $langId
     */
    public static function deleteDataLang($langId, $objectId)
    {
        $instance   = new static;
        $object     = $instance::where('object_id', $objectId)->first();

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

            $instance::where($object->table . '.object_id', $objectId)
                ->update([
                    'data_lang'  => json_encode($langArray)
                ]);
        }
    }
}