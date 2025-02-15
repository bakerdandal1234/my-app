<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    //
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description', 'is_completed','user_id','priority'];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tasks')->withPivot('is_favorite')
            ->withTimestamps();
    }
    

   
}
