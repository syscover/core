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