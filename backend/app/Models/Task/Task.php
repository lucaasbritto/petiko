<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
         'title', 'description', 'due_date', 'is_done', 'user_id',
         'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(){
        static::creating(function ($task) {
            $task->created_by = Auth::id();
            $task->updated_by = Auth::id();
        });

        static::updating(function ($task) {
            $task->updated_by = Auth::id();
        });

        static::deleting(function ($task) {
            $task->deleted_by = Auth::id();
            $task->save();
        });
    }
}
