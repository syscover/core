<?php
// routes angular application
Route::group(['prefix' => 'pulsar'],  function () {
    Route::any('{path}', function () {
        // get content from angular application index
        return File::get(public_path() . '/pulsar/index.html');
    })->where('path', '.+');
});