<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Agency represents the public body that owns several departments.
 */
class Agency extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cnpj',
        'address',
        'contact_email',
    ];

    /**
     * List departments for the agency.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
