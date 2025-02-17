<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي للمستخدم
     */
   

    /**
     * عرض ملف شخصي محدد
     */
    public function show()
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;
    
            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile not found'
                ], 404);
            }
            return response()->json([
                'message' => 'Success',
                'data' => $profile
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    /**
     * إنشاء ملف شخصي جديد
     */



     public function store(StoreProfileRequest $request)
     {
         $userId = Auth::user()->id; // الحصول على المستخدم الحالي
         $userValidated = $request->validated();
         $userValidated['user_id'] = $userId;
         if($request->hasFile('image')){
             $userValidated['image'] = $request->file('image')->store('my-images', 'public');
         }
         $profile = Profile::create($userValidated);
     
         return response()->json(['message' => 'Profile created', 'data' => $profile], 201);
     }

    /**
     * تحديث الملف الشخصي
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;
    
            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile not found'
                ], 404);
            }
           
             
            // Update profile with validated data
            $profile->update($request->validated());
    
            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => $profile
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * حذف الملف الشخصي
     */
    public function destroy()
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;
    
            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Profile not found'
                ], 404);
            }
            $profile->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'تم حذف الملف الشخصي بنجاح'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete profile',
                'error' => $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 500);
        }
    }
}
