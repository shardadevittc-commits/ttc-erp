<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    /**
     * UserRepository constructor.
     */
    public function __construct(
        protected User $model
    ) {
    }

    /**
     * Get all users.
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * Find user by ID.
     */
    public function find(int $id): ?User
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * Find user by ID or throw ModelNotFoundException.
     */
    public function findOrFail(int $id): User
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model
            ->newQuery()
            ->where('email', $email)
            ->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return $this->model->newQuery()->create($data);
        });
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return $user->fresh();
        });
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return (bool) $user->delete();
        });
    }

    /**
     * Restore a soft-deleted user.
     *
     * Requires SoftDeletes on the User model.
     */
    public function restore(int $id): bool
    {
        $user = $this->model
            ->newQuery()
            ->withTrashed()
            ->findOrFail($id);

        return (bool) $user->restore();
    }

    /**
     * Permanently delete a user.
     *
     * Requires SoftDeletes on the User model.
     */
    public function forceDelete(int $id): bool
    {
        $user = $this->model
            ->newQuery()
            ->withTrashed()
            ->findOrFail($id);

        return (bool) $user->forceDelete();
    }

    /**
     * Get paginated users.
     */
    public function paginate(
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Search users by name or email.
     */
    public function search(
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get active users.
     *
     * Requires an `is_active` column.
     */
    public function activeUsers(): Collection
    {
        return $this->model
            ->newQuery()
            ->where('is_active', true)
            ->get();
    }

    /**
     * Check whether email already exists.
     */
    public function emailExists(
        string $email,
        ?int $exceptUserId = null
    ): bool {
        return $this->model
            ->newQuery()
            ->where('email', $email)
            ->when(
                $exceptUserId,
                fn ($query) => $query->where('id', '!=', $exceptUserId)
            )
            ->exists();
    }

    /**
     * Count users.
     */
    public function count(): int
    {
        return $this->model->newQuery()->count();
    }
}