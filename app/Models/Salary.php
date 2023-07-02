<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'hours',
        'rate',
    ];

    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
