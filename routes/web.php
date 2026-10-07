<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// Division TODO: Groups
Route::get('/division', function () {
    return view('division');
});

Route::prefix('division')->group(function () {

    Route::get('/niisd', function () {
        return view('division.niisd');
    });

    Route::get('/database', function () {
        return view('division.database');
    });
        
    Route::get('/development', function () {
        return view('division.development');
    });

});
