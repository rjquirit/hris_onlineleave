<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'personnel_id');
    }

    protected $table = 'office_personnel';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'position',
        'department',
        'salary',
    ];
}
