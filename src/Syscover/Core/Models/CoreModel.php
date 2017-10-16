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
     * @param   $id
     * @param   $lang
     * @param   bool $deleteLangDataRecord
     * @return	void
     */
    public static function deleteTranslationRecord($id, $lang, $deleteLangDataRecord = true)
    {
        $instance = new static;

        $instance::where($instance->getKeyName(), $id)->where('lang_id', $lang)->delete();

        if($deleteLangDataRecord)
            $instance::deleteLangDataRecord($id, $lang);
    }


    /**
     * Manage data_lang column
     */

    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   int $id
     * @param   string $lang
     * @return	string
     */
    public static function addLangDataRecord($lang, $id = null)
    {
        // if id is equal to null, is a new object
        if($id === null)
        {
            $json[] = $lang;
        }
        else
        {
            $instance   = new static;
            $object     = $instance::find($id);

            if($object != null)
            {
                $json = $object->data_lang; // get data_lang from object
                $json[] = $lang; // add new language

                // updates all objects with new language variables
                $instance::where($object->table . '.' . $instance->getKeyName(), $id)
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
     * @param $id
     * @param $lang
     */
    public static function deleteLangDataRecord($id, $lang)
    {
        $instance   = new static;
        $object     = $instance::find($id);

        if($object != null)
        {
            $json = $object->data_lang;

            // unset isn't correct, get error to reorder array
            $langArray = [];
            foreach($json as $jsonLang)
            {
                if($jsonLang != $lang)
                {
                    $langArray[] = $jsonLang;
                }
            }

            $instance::where($object->table . '.' . $instance->getKeyName(), $id)
                ->update([
                    'data_lang'  => json_encode($langArray)
                ]);
        }
    }
}