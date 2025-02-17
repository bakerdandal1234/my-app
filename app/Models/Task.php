<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    //
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $fillable = ['title', 'description','priority','user_id','is_completed','is_favorite',];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'task_category');
    }

   
}
