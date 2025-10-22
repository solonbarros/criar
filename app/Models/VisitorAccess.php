<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * VisitorAccess keeps track of entries and exits with QR code tokens.
 */
class VisitorAccess extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'visitor_id',
        'department_id',
        'user_id',
        'entry_at',
        'exit_at',
        'qr_token',
        'purpose',
        'badge_path',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
    ];

    /**
     * Relationship with the visitor.
     */
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Relationship with the department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relationship with the registering user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
