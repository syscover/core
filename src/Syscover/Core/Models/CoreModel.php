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
     * @access	public
     * @param   array       $parameters             [id, lang]
     * @param   boolean     $deleteLangDataRecord
     * @return	void
     */
    public static function deleteTranslationRecord($parameters, $deleteLangDataRecord = true)
    {
        $instance = new static;

        $instance::where($instance->getKeyName(), $parameters['id'])->where('lang_id', $parameters['lang'])->delete();

        if($deleteLangDataRecord)
            $instance::deleteLangDataRecord($parameters);
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
        if($id === null)
        {
            $jsonString = json_encode(["langs" => [$lang]]);
        }
        else
        {
            $instance   = new static;
            $object     = $instance::find($id);

            if($object != null)
            {
                $jsonObject             = json_decode($object->{'data_lang'});
                $jsonObject->langs[]    = $lang;
                $jsonString             = json_encode($jsonObject);

                // updates all objects with new language variables
                $instance::where($instance->getKeyName(), $id)->update([
                    'data_lang' => $jsonString
                ]);
            }
            else
            {
                $jsonString = '{"langs":["' . $lang . '"]}';
            }

        }

        return $jsonString;
    }

    /**
     *  Function to delete lang record from json field
     *
     * @access  public
     * @param   array   $parameters     [id, lang]
     * @return  void
     */
    public static function deleteLangDataRecord($parameters)
    {
        $instance   = new static;
        $object     = $instance::find($parameters['id']);

        if($object != null)
        {
            $jsonObject = json_decode($object->{'data_lang'});

            // unset isn't correct, get error to reorder array
            $newArrayLang = [];
            foreach($jsonObject->langs as $keyLang)
            {
                if($keyLang != $parameters['lang'])
                {
                    $newArrayLang[] = $keyLang;
                }
            }
            $jsonObject->langs = $newArrayLang;
            $jsonString = json_encode($jsonObject);

            $instance::where($instance->getKeyName(), $parameters['id'])->update([
                'data_lang'  => $jsonString
            ]);
        }
    }
}