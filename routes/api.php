<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
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

// Route::get('tasks', [\App\Http\Controllers\Api\TaskController::class, 'index']); 

Route::group(['prefix'=>'v1','namespace'=>'App\Http\Controllers\Api\V1'],function(){
    Route::apiResource('/tasks',TaskController::class)->middleware(['auth:sanctum', 'can:viewAny,App\Models\Task']); 
    
    Route::post('/register', [AuthController::class,'register']); 
    Route::post('/login', [AuthController::class,'login']); 
    Route::post('/logout', [AuthController::class,'logout']); 
  });

// Route::post('/tasks', [App\Http\Controllers\Api\V1\TaskController::class, 'store'])->middleware('auth:sanctum');



// Route::getJson('/tasks', [App\Http\Controllers\Api\V1\TaskController::class, 'index']);