<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'description',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
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
            'status' => 'integer',
        ];
    }

    /**
     * Role => permissions map.
     *
     * This project currently stores roles in the `roles` table and does not have
     * a separate permissions table or pivot relationship, so permissions are
     * modeled as role-based permissions using the existing schema.
     */
    public static function permissionMap(): array
    {
        return [
            'admin' => [
                'users.view', 'users.create', 'users.update', 'users.delete',
                'roles.view', 'roles.manage', 'settings.view',
            ],
            'sale' => [
                'sales.view', 'sales.create', 'sales.update', 'sales.delete',
            ],
            'purchase' => [
                'purchases.view', 'purchases.create', 'purchases.update', 'purchases.delete',
            ],
            'gate' => [
                'gate.view', 'gate.create', 'gate.update', 'gate.delete',
            ],
            'weight' => [
                'weight.view', 'weight.create', 'weight.update', 'weight.delete',
            ],
            'unloader' => [
                'unloading.view', 'unloading.create', 'unloading.update', 'unloading.delete',
            ],
            'dispatch' => [
                'dispatch.view', 'dispatch.create', 'dispatch.update', 'dispatch.delete',
            ],
            'lab' => [
                'lab.view', 'lab.create', 'lab.update', 'lab.delete',
            ],
            'production' => [
                'production.view', 'production.create', 'production.update', 'production.delete',
            ],
            'lab_production' => [
                'lab_production.view', 'lab_production.create', 'lab_production.update', 'lab_production.delete',
            ],
            'account' => [
                'accounting.view', 'accounting.create', 'accounting.update', 'accounting.delete',
            ],
        ];
    }

    /**
     * Get the permission list for this role.
     */
    public function permissions(): Collection
    {
        return collect(static::permissionMap()[$this->short_name] ?? []);
    }

    /**
     * Check whether this role has a given permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->contains($permission);
    }

    /**
     * Get the users associated with the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
