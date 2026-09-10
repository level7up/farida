<?php

namespace App\Domain\Organization\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $table = 'organization_user';

    protected $fillable = [
        'user_id',
        'organization_id',
        'role',
    ];

    protected $casts = [
        'role' => 'string',
    ];

    const ROLES = [
        'Owner',
        'Admin',
        'Manager',
        'Staff',
        'Accountant',
        'Cleaning',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
