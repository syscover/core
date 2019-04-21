<?php namespace Syscover\Core\Traits;

trait CanManageDataLang
{
    /**
     * Function to add lang record from json field
     *
     * @access	public
     * @param   string     $langId
     * @param   int     $id
     * @param   array   $filters            filters to select and updates records
     * @return	string
     */
    public static function getDataLang(
        string $langId,
        $id = null,
        array $filters = []
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
                    'data_lang' => json_encode($langArray)
                ]);
        }
    }
}
