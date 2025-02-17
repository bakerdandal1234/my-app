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

    public function isCompleted($task_id)
    {
        $task = Task::findOrFail($task_id);
        $user = Auth::user();

        if ($task->user_id !== $user->id) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }

        $task->update(['is_completed' => true]);

        return response()->json([
            'message' => 'Task completed successfully',
            'data' => $task
        ], 200);
    }

    public function isNotCompleted($task_id)
    {
        $task = Task::findOrFail($task_id);
        $user = Auth::user();
        if ($task->user_id != $user->id) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }
        $task->update(['is_completed' => false]);

        return response()->json([
            'message' => 'Task not completed successfully',
            'data' => $task
        ], 200);
    }

    public function getAllTaskCompleted()
    {
        $tasks = Auth::user()->tasks()->where('is_completed', true)->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);
    }


    public function addToFavorite($task_id)
    {

        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // logged-in user

        if ($task->user_id !== $user->id) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }


        $task->update(['is_favorite' => true]);


        return response()->json([
            'message' => 'Task added to favorites successfully',
            'data' => $task
        ], 201);
    }

    public function deleteFromFavorite($task_id)
    {
        $task = Task::findOrFail($task_id);
        $user = Auth::user(); // logged-in user

        if ($task->user_id != $user->id) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }

        $task->update(['is_favorite' => false]);
        return response()->json([
            'message' => 'Task deleted from favorites successfully',
            'data' => $task
        ], 201);
    }

    public function getAllFavorites()
    {

        $tasks = Auth::user()->tasks()->where('is_favorite', true)->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);
    }


    public function getTaskByPriorityDesc()
    {
        $user = Auth::user();
        $tasks = $user->tasks()->orderBy('priority', 'desc')->get();

        return response()->json([
            'message' => $tasks->isEmpty() ? 'لا توجد مهام حالياً' : 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);
    }


    public function getTaskByPriorityAsc()
    {
        $user = Auth::user();
        $tasks = $user->tasks()->orderBy('priority', 'asc')->get();

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

    public function index()
    {
        try {
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
    public function store(StoreTaskRequest $request)
    {
        try {
            $user_id = Auth::user()->id;
            $validateData = $request->validated();
            $validateData['user_id'] = $user_id;
            $task = Task::create($validateData);
            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل انشاء المهمة', 'message' => $e->getMessage()], 500);
        }
    }


    // i comment this code because the relation here  is many to many any user can update any task 
    public function update(UpdateTaskRequest $request, $task_id)
    {
        $user_id = Auth::user()->id;
        $task = Task::findOrFail($task_id);
        if ($task->user_id != $user_id) {
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
    }



    /**
     * جلب مهمة معينة من قاعدة البيانات
     * 
     * @param int $user_id رقم المستخدم (user_id)
     * @param int $task_id رقم المهمة (task_id)
     * GET /users/1/tasks/1 - يجل المهمة رقم 1 لمستخدم رقم 1
     */
    public function show($task_id)
    {
        $user_id = Auth::user()->id;
        $task = Task::findOrfail($task_id);
        if ($task->user_id != $user_id) {
            return response()->json([
                'message' => 'غير مصرح بالتعديل',
                'error' => 'Unauthorized'
            ], 403);
        }
        return response()->json([
            'message' => 'تم جلب المهمة بنجاح',
            'data' => $task
        ], 200);
    }
    /** @param int $user_id رقم المستخدم (user_id)
     * @param int $task_id رقم المهمة (task_id)
     * DELETE /users/1/tasks/1 - حذف المهمة رقم 1 لمستخدم رقم 1
     */
    public function destroy($task_id)
    {
        try {
            $task = Task::findOrFail($task_id);
            $user_id = Auth::user()->id;
            if ($task->user_id != $user_id) {
                return response()->json([
                    'message' => 'غير مصرح بالتعديل',
                    'error' => 'Unauthorized'
                ], 403);
            }
            $task->delete();
            return response()->json([
                'message' => 'تم حذف المهمة بنجاح',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في حذف المهمة',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
