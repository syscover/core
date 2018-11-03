<?php namespace Syscover\Core\Services;

class SlugService
{
    /**
     *  Function to check if slug exists
     *
     * @access  public
     * @param   string          $model
     * @param   string          $slug
     * @param   string          $column
     * @param   integer|string  $id
     * @param   null|string     $lang_id
     * @return  string          $slug
     */
    public static function checkSlug($model, $slug, $id = null, $column = 'slug', $lang_id = null)
    {
        $slug   = str_slug($slug);
        $model  = new $model;

        $query = $model->where($column, $slug);
        if ($lang_id !== null) $query->where('lang_id', $lang_id);
        if ($id !== null) $query->whereNotIn($model->getKeyName(), [$id]);
        $n = $query->count();

        if ($n > 0) {
            $suffix = 0;
            while ($n > 0) {
                $suffix++;
                $slug = $slug . '-' . $suffix;

                $query = $model->where($column, $slug);
                if ($id !== null) $query->whereNotIn($model->getKeyName(), [$id]);
                $n = $query->count();
            }
        }

        return $slug;
    }
}