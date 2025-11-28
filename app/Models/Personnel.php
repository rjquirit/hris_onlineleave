<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedAttributes;

class Personnel extends Model
{
    use HasFactory, HasEncryptedAttributes;

    public function user()
    {
        return $this->hasOne(User::class, 'personnel_id');
    }

    /**
     * Get the list of encrypted attributes for this model.
     *
     * @return array
     */
    protected function getEncryptedAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'position', 'department', 'salary'];
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'first_name_search_index',
        'last_name_search_index',
        'email_search_index',
        'position_search_index',
        'department_search_index',
        'salary_search_index',
    ];
}
