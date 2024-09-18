<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeController;
use App\Models\Category;
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


Route::prefix('/v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/recipes', [HomeController::class, 'recipes']);
    Route::get('/best-recipes', [HomeController::class, 'top']);
    Route::get('/recipe/{slug}', [HomeController::class, 'recipe']);
    Route::get('/categories', [HomeController::class, 'categories']);
    Route::middleware('user')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::middleware('admin')->group(function () {
            Route::post('categories', [CategoryController::class, 'store']);
            Route::delete('/categories/{slug}', [CategoryController::class, 'destroy']);
        });
        Route::post('/recipes', [RecipeController::class, 'store']);
        Route::delete('/recipes/{slug}', [RecipeController::class, 'destroy']);
        Route::post('/recipes/{slug}/rating', [RecipeController::class, 'rating']);
        Route::post('/recipes/{slug}/comment', [RecipeController::class, 'comment']);
    });
});
