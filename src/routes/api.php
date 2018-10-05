<?php

// LOCAL GRAPHQL
Route::group(['middleware' => ['web']], function () {
    Route::post('graphql/localhost', 'Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController@query')->name('graphql.localhost');
});
