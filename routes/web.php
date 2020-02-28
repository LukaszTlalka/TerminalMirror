<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('tutorial');
});

Route::get('/new-session', 'SessionController@create');
Route::get('/terminal', 'SessionController@terminal');

if (env('APP_ENV') == 'local') {
    Route::get('/debug/files', 'DebugController@files');
    Route::get('/debug/files-data', 'DebugController@filesData');
}
