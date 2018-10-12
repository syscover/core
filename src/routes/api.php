<?php

// LOCAL GRAPHQL
// to consume this API URL you need send a X-CSRF-TOKEN in each request
Route::group(['middleware' => ['web']], function () {
    Route::post('graphql/localhost', 'Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController@query')->name('graphql.localhost');
});
