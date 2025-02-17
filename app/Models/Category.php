<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = ['name', 'description',];
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public function tasks()
{
    return $this->belongsToMany(Task::class, 'task_category');
}
}
