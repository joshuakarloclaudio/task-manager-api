<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'completed' => 'bool'
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'completed',
    ];
}
