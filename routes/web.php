<?php

/*
|----------------------------------
| bootstrap PULSAR
|----------------------------------
*/
Route::get('pulsar',  function () {
    return view('core::app.index');
});