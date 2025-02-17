<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\CheckUserRole;
use App\Models\Category;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// مسارات الملف الشخصي
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::put('/', [ProfileController::class, 'update']);
    // حذف البروفايل
    Route::delete('/', [ProfileController::class, 'destroy']);

    // استرجاع البروفايل
    Route::get('/', [ProfileController::class, 'show']);

    // إنشاء بروفايل (إذا لزم الأمر)
    Route::post('/', [ProfileController::class, 'store']);
});


Route::get('categories',[CategoryController::class,'index']);
Route::post('categories',[CategoryController::class,'store']);
Route::post('tasks/{task_id}/categories',[CategoryController::class,'AddCategoryToTask']);
Route::get('tasks/{task_id}/categories',[CategoryController::class,'getCategoriesByTask']);
Route::get('categories/{category_id}/tasks',[CategoryController::class,'getTasksByCategory']);
Route::get('categories/{id}',[CategoryController::class,'show']);
Route::put('categories/{id}',[CategoryController::class,'update']);
Route::delete('categories/{id}',[CategoryController::class,'destroy']);

Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::put('/{task_id}', [TaskController::class, 'update']);
    Route::delete('/{task_id}', [TaskController::class, 'destroy']);

    // قيد على task_id لقبول أرقام فقط
    Route::get('/{task_id}', [TaskController::class, 'show'])
        ->where('task_id', '[0-9]+');

    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);


    Route::get('/orderedDesc', [TaskController::class, 'getTaskByPriorityDesc']);
    Route::get('/orderedAsc', [TaskController::class, 'getTaskByPriorityAsc']);


    Route::put('/{task_id}/favorite', [TaskController::class, 'addToFavorite']);

    Route::delete('/{task_id}/favorite', [TaskController::class, 'deleteFromFavorite']);

    Route::get('/favorite', [TaskController::class, 'getAllFavorites']);


    Route::get('/all', [TaskController::class, 'getAllTasks'])->middleware(CheckUserRole::class);

    Route::put('/{task_id}/completed', [TaskController::class, 'isCompleted']);

    Route::put('/{task_id}/not-completed', [TaskController::class, 'isNotCompleted']);

    Route::get('/completed', [TaskController::class, 'getAllTaskCompleted']);
});
