<?php

if (! function_exists('trans_has')) {
    /**
     * Determine if a translation exists.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return bool
     */
    function trans_has($key, $locale = null, $fallback = true)
    {
        return app('translator')->has($key, $locale, $fallback);
    }
}

if (! function_exists('date_time_string')) {
    /**
     * Transform javascript date to timestamp
     *
     * @param  string  $value
     * @return string
     */
    function date_time_string($value)
    {
        // use preg_replace to format date from Google Chrome, attach (Hota de verano romance) string
        return (new \Carbon\Carbon(preg_replace('/\(.*\)/','', $value), config('app.timezone')))->toDateTimeString();
    }
}

if (! function_exists('carbon')) {
    /**
     * Create Carbon from format
     *
     * @param  string  $value
     * @param  string  $format
     * @return \Carbon\Carbon
     */
    function carbon($value, $format = 'Y-m-d\TH:i:s')
    {
        return \Carbon\Carbon::createFromFormat($format, $value);
    }
}

if (! function_exists('object_lang')) {
    /**
     * Get object with current language
     *
     * @param   \Illuminate\Support\Collection $objects
     * @param   null $lang_id
     * @return  mixed
     */
    function object_trans(\Illuminate\Support\Collection $objects, $lang_id = null)
    {
        return $objects->where('lang_id', $lang_id ?? user_lang())->first();
    }
}

if (! function_exists('next_id')) {
    /**
     * Get next id from model
     *
     * @param  string  $model
     * @param  string  $key
     * @return int
     */
    function next_id($model, $key = 'id')
    {
        $model  = new $model;
        $id     = $model->max($key);
        $id++;

        return $id;
    }
}