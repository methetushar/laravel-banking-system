<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;


Route::post('/users', [UserController::class, 'create']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function (){
    Route::get('/user', [UserController::class, 'user']);
    Route::get('/', [TransactionController::class, 'showTransactions']);
    Route::get('/deposit', [TransactionController::class, 'showDeposits']);
    Route::post('/deposit', [TransactionController::class, 'deposit']);
    Route::get('/withdrawal', [TransactionController::class, 'showWithdrawals']);
    Route::post('/withdrawal', [TransactionController::class, 'withdrawal']);
});


