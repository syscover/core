<?php namespace Syscover\Core\Services;

class SlugService
{
    /**
     *  Function to check if slug exists
     *
     * @access  public
     * @param   string          $model
     * @param   string          $slug
     * @param   string          $field
     * @param   integer|string  $id
     * @return  string          $slug
     */
    public static function checkSlug($model, $slug, $id = null, $field = 'slug')
    {
        $slug   = str_slug($slug);
        $model  = new $model;

        $query = $model->where($field, $slug);
        if ($id !== null) $query->whereNotIn($model->getKeyName(), [$id]);
        $n = $query->count();

        if ($n > 0) {
            $suffix = 0;
            while ($n > 0) {
                $suffix++;
                $slug = $slug . '-' . $suffix;

                $query = $model->where($field, $slug);
                if ($id !== null) $query->whereNotIn($model->getKeyName(), [$id]);
                $n = $query->count();
            }
        }

        return $slug;
    }
}