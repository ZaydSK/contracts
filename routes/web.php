<?php

use App\Http\Controllers\ContractController;
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

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('contracts')->group( function(){
    Route::get('/', [ContractController::class,'all']);
    Route::get('/{contract}', [ContractController::class,'one']);
    Route::post('/', [ContractController::class,'add']);
    Route::get('/{contract}/materials', [ContractController::class,'materials']);
});