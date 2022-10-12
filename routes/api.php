<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('test', function(){
    dd('123');
})->name('test');

Route::get('/image-upload', [PageController::class , 'displayImage'])->name('image.test');
Route::post('/image-upload', [PageController::class , 'uploadImage'])->name('image.test');
Route::delete('/image-upload/{id}' , [PageController::class , 'destroy'])->name('image.delete');
Route::post('/image-upload/{id}', [PageController::class , 'updateImage'])->name('image.test');