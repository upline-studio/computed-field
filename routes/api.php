<?php

use Illuminate\Support\Facades\Route;

Route::post('/calculate/{resource}', 'ComputedFieldController@calculate')->name('upline.computed-field.calculate');
