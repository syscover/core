<?php namespace Syscover\Core\Traits;

use Illuminate\Support\Facades\DB;

trait CanManageCrud
{
    protected $table;

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
}