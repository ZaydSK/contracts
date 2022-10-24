<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\MaterialController;
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

Route::prefix('contracts')->group( function(){
    Route::get('/', [ContractController::class,'all']);
    Route::get('/{contract}', [ContractController::class,'one']);
    Route::post('/', [ContractController::class,'add']);
    Route::get('/{contract}/materials', [ContractController::class,'materials']);
});

