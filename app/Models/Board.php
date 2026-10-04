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
        'tag',
        'owner_id'
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
}
