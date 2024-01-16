<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PlayerController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // AuthController
    Route::get('/player', [PlayerController::class, 'getPlayer']);
    Route::post('/player', [PlayerController::class, 'createPlayer']);
    Route::delete('/player', [PlayerController::class, 'deletePlayer']);
    Route::get('player-location', [PlayerController::class, 'getMyLocation']);
    Route::post('player-location', [PlayerController::class, 'setMyLocation']);
    Route::get('players-around', [PlayerController::class, 'getPlayerNear']);

    Route::get('my-set', [PlayerController::class, 'getMySet']);
    Route::post('/pick-item', [ItemController::class, 'pickItem']);
    Route::get('item-bag', [PlayerController::class, 'getItemsbag']);
    Route::post('/equip-item', [PlayerController::class, 'equipItem']);

    Route::post('sell-item', [PlayerController::class, 'sellItem']);
    Route::post('drop-item', [PlayerController::class, 'dropItem']);
    Route::post('consume-item', [PlayerController::class,'consumeitem']);

    
    Route::post('hit-me', [PlayerController::class,'hitme']);

});

Route::get('main_classes', [PlayerController::class, 'getClassesMain']);
Route::get('sec_classes', [PlayerController::class, 'getClassesSecondary']);



Route::get('/item', [ItemController::class, 'index']);
Route::get('/item/{id}', [ItemController::class, 'show']);
Route::post('/item', [ItemController::class, 'store']);
Route::delete('/vulk-item', [ItemController::class, 'overdestroy']);

Route::post('/upload-img', [ItemController::class, 'uploadimage']);



