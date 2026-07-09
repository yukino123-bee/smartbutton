<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ndrrmo', function () {
    return view('ndrrmo');
});

Route::get('/clinic', function () {
    return view('clinic');
});
