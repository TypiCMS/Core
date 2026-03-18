<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-write mixed $status
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 */
class Role extends SpatieRole implements RoleContract
{
    use HasAdminUrls;
    use Historable;

    protected $guarded = [];

    public function presentTitle(): string
    {
        return $this->name;
    }
}
