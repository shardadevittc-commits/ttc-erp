<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',    
        'role_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone_number',
        'address',
        'dob',
        'image',
        'country_id',
        'state_id',
        'city_id',
        'zipcode',
        'logs',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'logs' => 'array',
        'status' => 'integer',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'logs' => 'array',
            'status' => 'integer',
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permissions assigned to this user through their role.
     *
     * This project currently uses the existing `roles` table and `role_id` field,
     * so no separate permissions table or pivot is required.
     */
    public function permissions(): Collection
    {
        return $this->role ? $this->role->permissions() : collect();
    }

    /**
     * Check whether the current user has a given permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->contains($permission);
    }

    /**
     * Accessor for the permission list.
     *
     * Enables the common usage pattern:
     * Auth::user()->permissions
     */
    public function getPermissionsAttribute(): Collection
    {
        return $this->permissions();
    }

    /**
     * Accessor for the user's role.
     *
     * Enables the common usage pattern:
     * Auth::user()->role
     */
    public function getRoleAttribute(): ?Role
    {
        return $this->role()->first();
    }

    /**
     * Get the full name for the user.
     */
    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Get the user's avatar image URL or fallback placeholder.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        $name = $this->name ?: 'User';
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0B1220&color=ffffff&bold=true';
    }
}
