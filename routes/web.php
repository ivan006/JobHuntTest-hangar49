<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/welcome', function () {
    return view('welcome');
});
Route::get('/', 'customer_c@index');
// Route::post('/', 'customer_c@index');
Route::get('/SyncGoogleSheetsToLocalDB', 'customer_c@SyncGoogleSheetsToLocalDB');
Route::get('/SyncLocalDBToHubspot', 'customer_c@SyncLocalDBToHubspot');
Route::get('/SyncHubspotToLocalDB', 'customer_c@SyncHubspotToLocalDB');
Route::get('/SyncLocalDBToWoodpecker', 'customer_c@SyncLocalDBToWoodpecker');
Route::post('/update', 'customer_c@update');
Route::get('/hubspot_migration', 'customer_c@hubspot_migration');
