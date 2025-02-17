<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
class CategoryController extends Controller
{
    //
    /**
     * جلب المهام التي تنتمي الي تصنيف معين
     * 
     * @param int $category_id رقم التصنيف (category_id)
     * GET /categories/1/tasks - يجل المهام للتصنيف رقم 1
     */
    public function getTasksByCategory($category_id){
        $category = Category::findOrFail($category_id);
        $tasks = $category->tasks;
        return response()->json([
            'message' => 'تم جلب المهام بنجاح',
            'data' => $tasks
        ], 200);
    }
    /**
     * جلب التصنيفات التي تنتمي الي مهمة معينة
     * 
     * @param int $task_id رقم المهمة (task_id)
     * GET /tasks/1/categories - يجل التصنيفات لمهمة رقم 1
     */
    public function getCategoriesByTask($task_id){
        $task = Task::findOrFail($task_id);
        $categories = $task->categories;
        return response()->json([
            'message' => 'تم جلب التصنيفات بنجاح',
            'data' => $categories
        ], 200);
    }
    /**
     * اضافة تصنيف الي مهمة معينة
     * 
     * @param int $task_id رقم المهمة (task_id)
     * @param int $category_id رقم التصنيف (category_id)
     * POST /tasks/1/categories/1 - اضافة التصنيف رقم 1 الي المهمة رقم 1
     */
    public function AddCategoryToTask(Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->categories()-> attach($request->category_id);
        return response()->json([
            'message' => 'تم اضافة التصنيف الي المهمة بنجاح',
            'data' => $task
        ], 200);
    }

    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $categories
        ], 200);
    }


public function store(StoreCategoryRequest $request)
{
    $category = Category::create($request->validated());
    return response()->json([
        'message' => 'تم انشاء التصنيف بنجاح',
        'data' => $category
    ], 201);
}
   

public function show($id)
{
    $category = Category::findOrFail($id);
    return response()->json([
        'message' => 'تم جلب التصنيف بنجاح',
        'data' => $category
    ], 200);
}

public function update(UpdateCategoryRequest $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update($request->validated());
    return response()->json([
        'message' => 'تم تحديث التصنيف بنجاح',
        'data' => $category
    ], 200);
}


public function destroy($id)
{
    $category = Category::findOrFail($id);
    $category->delete();
    return response()->json([
        'message' => 'تم حذف التصنيف بنجاح',
        'data' => $category
    ], 200);

}

}

