<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'category_id',
        'owner_id',
        'show_task_tags',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function historic()
    {
        return $this->hasOne(Historic::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getTagAttribute()
    {
        return $this->category?->name;
    }
}
