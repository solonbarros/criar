<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Department belongs to an agency and hosts users and access records.
 */
class Department extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'agency_id',
        'floor',
        'room',
    ];

    /**
     * Agency relationship.
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Users relationship.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Access records relationship.
     */
    public function accesses()
    {
        return $this->hasMany(VisitorAccess::class);
    }
}
