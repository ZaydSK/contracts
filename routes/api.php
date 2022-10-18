<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/contracts', [ContractController::class,'all']);
Route::get('/contracts/{contract}', [ContractController::class,'all']);
Route::post('/contracts', [ContractController::class,'add']);
Route::get('/contracts/{contract}/materials', [MaterialController::class,'all']);
Route::post('/contracts/{contract}/materials', [MaterialController::class,'add']);
