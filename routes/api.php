<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;

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


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('insecure.jwt')->put('/profile', [AuthController::class, 'updateProfile']);
// Route::middleware('insecure.jwt')->get('/me', [AuthController::class, 'me']);
// Route::middleware('insecure.jwt')->post('/admin/promote', [AuthController::class, 'promoteUser']);



// // Book routes
// Route::get('/books', [BookController::class, 'index']);
// Route::get('/books/search', [BookController::class, 'search']);
// Route::middleware('insecure.jwt')->post('/books', [BookController::class, 'store']);
// Route::middleware('insecure.jwt')->put('/books/{id}', [BookController::class, 'update']);
// Route::middleware('insecure.jwt')->get('/books/{id}', [BookController::class, 'show']);
// Route::middleware('insecure.jwt')->delete('/books/{id}', [BookController::class, 'destroy']);

// // Review routes
// Route::middleware('insecure.jwt')->post('/books/{id}/reviews', [ReviewController::class, 'store']);
// Route::middleware('insecure.jwt')->delete('/reviews/{id}', [ReviewController::class, 'destroy']);



// Route::middleware('insecure.jwt')->group(function () {
//     Route::get('admin/categories', [CategoryController::class, 'index']);
//     Route::post('admin/categories', [CategoryController::class, 'store']);
//     Route::put('admin/categories/{id}', [CategoryController::class, 'update']);
//     Route::post('admin/books/{id}/approve', [BookController::class, 'approve']);
//     Route::delete('admin/categories/{id}', [CategoryController::class, 'destroy']);
//     Route::put('admin/reviews/{id}/approve', [ReviewController::class, 'approve']);
// });


// Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// Route::post('/reset-password', [AuthController::class, 'resetPassword']);





// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public Book Routes
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/search', [BookController::class, 'search']);

// Protected Routes (Requires JWT Authentication)
Route::middleware('insecure.jwt')->group(function () {

    // User Profile & Info
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/admin/promote', [AuthController::class, 'promoteUser']);

    // Book Management
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    // Review Management
    Route::post('/books/{id}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        // Category Management
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Book Approval
        Route::post('/books/{id}/approve', [BookController::class, 'approve']);

        // Review Approval
        Route::put('/reviews/{id}/approve', [ReviewController::class, 'approve']);
    });
});
