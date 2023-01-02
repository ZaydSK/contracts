<?php

use App\Http\Controllers\ContractController;
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
    Route::post('/{contract}/bills', [ContractController::class,'addBill']);
    Route::get('/{contract}/bills', [ContractController::class,'allBills']);
    Route::post('/{contract}/subs', [ContractController::class,'addSub']);
    Route::get('/{contract}/subs', [ContractController::class,'subs']);
    Route::get('/{contract}/bills/{bill}', [ContractController::class,'oneBill']);
    Route::get('/', [ContractController::class,'all']);
    Route::get('/{contract}', [ContractController::class,'one']);
    Route::post('/', [ContractController::class,'add']);
    Route::post('/search', [ContractController::class,'search']);
    Route::get('/{contract}/materials', [ContractController::class,'materials']);
    Route::get('/{contract}/increases', [ContractController::class,'increases']);
    Route::post('/{contract}/increases', [ContractController::class,'addIncrease']);


});

