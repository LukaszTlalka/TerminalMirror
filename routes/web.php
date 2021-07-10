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

Route::get('/commands', function () {
    for ($i = 0; $i < 3; $i++) {
        echo "ls\n";
        sleep(1);
        flush();
        ob_flush();
    }
});

Route::get('/', function () {
    return view('tutorial');
});

Route::get('/new-session', 'SessionController@create');
Route::get('/check-curl-session/{hash}', 'SessionController@checkCurlSession')->name('check-curl-session');
Route::get('/terminal/{hash}', 'SessionController@terminal')
    ->where(['hash' => '([a-f0-9]{32})', 'name' => '[a-z]+'])
    ->name('terminal-session');

if (env('APP_ENV') == 'local') {
    Route::get('/debug/files', 'DebugController@files');
    Route::get('/debug/files-data', 'DebugController@filesData');
}
