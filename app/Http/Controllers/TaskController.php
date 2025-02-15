<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller

{

    public function isCompleted($task_id){
        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // logged-in user

        // Update the pivot table using updateExistingPivot
        $user->tasks()->updateExistingPivot($task_id, [
            'is_completed' => true
        ]);

        return response()->json([
            'message' => 'Task completed successfully',
            'data' => $task
        ], 201);
    }

    public function isNotCompleted($task_id){
        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // logged-in user

        // Update the pivot table using updateExistingPivot
        $user->tasks()->updateExistingPivot($task_id, [
            'is_completed' => false
        ]);

        return response()->json([
            'message' => 'Task not completed successfully',
            'data' => $task
        ], 201);
    }

    public function getAllTaskCompleted(){
        $tasks = Task::whereHas('users', function ($query) {
            $query->where('user_id', Auth::user()->id)
                ->where('is_completed', true);
        })->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);
    }


   public function addToFavorite($task_id){
   
        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // logged-in user

        

        // Update the pivot table using updateExistingPivot
        $user->tasks()->updateExistingPivot($task_id, [
            'is_favorite' => true
        ]);

        return response()->json([
            'message' => 'Task added to favorites successfully',
            'data' => $task
        ], 201);
}

public function deleteFromFavorite($task_id){
    $task = Task::findOrFail($task_id);
    $user = Auth::user(); // logged-in user

   

    $user->tasks()->updateExistingPivot($task_id, [
        'is_favorite' => false
    ]);

    return response()->json([
        'message' => 'Task deleted from favorites successfully',
        'data' => $task
    ], 201);
}

public function getAllFavorites(){
   
        $tasks = Task::whereHas('users', function ($query) {
            $query->where('user_id', Auth::user()->id)
                ->where('is_favorite', true);
        })->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);

    
}

            
   public function getTaskByPriorityDesc(){
   
        $tasks = Task::orderBy('priority', 'desc')->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);

   }


   public function getTaskByPriorityAsc(){
   
        $tasks = Task::orderBy('priority', 'asc')->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);

   }
   // here we dont use middleware userRole because relatioship many to many ,every user can edit any task 
    public function getAllTasks()
{
    try {
        $tasks = Task::all();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);

    } catch (\Exception $e) {
        Log::error('Tasks Error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'خطأ في النظام',
            'message' => 'حدث خطأ تقني، يرجى المحاولة لاحقاً'
        ], 500);
    }
}

    public function index(){
        try{
            $tasks = Auth::user()->tasks;
            return response()->json([
                'message' => 'تم جلب المهام بنجاح',
                'data' => $tasks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في جلب المهام',
                'message' => $e->getMessage()
            ], 500);
        }
    }
   public function store(StoreTaskRequest $request){
    try {
        $user_id= Auth::user()->id;
        $task = Task::create($request->validated());
        $task->users()->attach($user_id);
        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ],201);}
    catch (\Exception $e) {
        return response()->json(['error' => 'فشل انشاء المهمة', 'message' => $e->getMessage()], 500);
    }
}



public function update(UpdateTaskRequest $request, $task_id)
{
    try {
        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // user that login

        // التحقق من وجود المستخدم في جدول ال pivot
        if (!$task->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }

        $task->update($request->validated());

        return response()->json([
            'message' => 'تم تحديث المهمة بنجاح',
            'data' => $task
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'error' => 'المهمة غير موجودة',
            'message' => 'لم يتم العثور على المهمة المطلوبة'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'فشل في التحديث',
            'message' => $e->getMessage()
        ], 500);
    }
}



/**
 * جلب مهمة معينة من قاعدة البيانات
 * 
 * @param int $user_id رقم المستخدم (user_id)
 * @param int $task_id رقم المهمة (task_id)
 * GET /users/1/tasks/1 - يجل المهمة رقم 1 لمستخدم رقم 1
 */
public function show( $task_id) {
    try{
        $task= Task::findOrfail($task_id);
        $user=Auth::user();
        if (!$task->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'you can not show this task',
                'error' => 'Unauthorized'
            ], 403);
        }
        // $task = Task::find($task_id)->users()->findOrFail($user_id);
        return response()->json([
            'message' => 'تم جلب المهمة بنجاح',
            'data' => $task
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'فشل في جلب المهمة',
            'message' => $e->getMessage()
        ], 404);
    }
    
}
/** @param int $user_id رقم المستخدم (user_id)
 * @param int $task_id رقم المهمة (task_id)
 * DELETE /users/1/tasks/1 - حذف المهمة رقم 1 لمستخدم رقم 1
 */
public function destroy($task_id){
    try{
        $task = Task::find($task_id);
        $user=Auth::user();
        if (!$task->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'you can not delete this task',
                'error' => 'Unauthorized'
            ], 403);
        }
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully',
            'data' => $task
        ],200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Task not found'], 404);
    }
   
}
}
