<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Visitor keeps personal data captured during entrance procedures.
 */
class Visitor extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'document_number',
        'email',
        'phone',
        'photo_path',
        'photo_hash',
        'consent_at',
        'anonymized_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'consent_at' => 'datetime',
        'anonymized_at' => 'datetime',
    ];

    /**
     * Determine if the visitor has been anonymized.
     */
    public function isAnonymized(): bool
    {
        return $this->anonymized_at instanceof Carbon;
    }

    /**
     * Access records relationship.
     */
    public function accesses()
    {
        return $this->hasMany(VisitorAccess::class);
    }
}
