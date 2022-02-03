<?php
if (! function_exists('content')) {
    /**
     * Get / set the specified content value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\App\Repository
     */
    function content($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('content');
        }

        if (is_array($key)) {
            return app('content')->set($key);
        }

//        if( \Illuminate\Support\Facades\File::exists() )

        //allow for markdown reading too

        return app('content')->get($key, $default);
    }
}
