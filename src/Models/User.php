<?php

namespace TypiCMS\Modules\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laracasts\Presenter\PresentableTrait;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use TypiCMS\Modules\Core\Notifications\ResetPassword;
use TypiCMS\Modules\Core\Notifications\VerifyEmail;
use TypiCMS\Modules\Core\Presenters\UsersPresenter;
use TypiCMS\Modules\Core\Traits\Historable;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property bool $activated
 * @property bool $superuser
 * @property bool $privacy_policy_accepted
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $phone
 * @property string|null $locale
 * @property string|null $street
 * @property string|null $number
 * @property string|null $box
 * @property string|null $postal_code
 * @property string|null $city
 * @property string|null $country
 * @property array<array-key, mixed>|null $preferences
 * @property string $api_token
 * @property string|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $all_permissions
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, HasLocalePreference, MustVerifyEmailContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasRoles;
    use Historable;
    use MustVerifyEmail;
    use Notifiable;
    use PresentableTrait;

    protected string $presenter = UsersPresenter::class;

    protected $guarded = ['my_name', 'my_time'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferences' => 'array',
            'superuser' => 'boolean',
            'activated' => 'boolean',
            'privacy_policy_accepted' => 'boolean',
        ];
    }

    protected function getDefaultGuardName(): string
    {
        // https://github.com/spatie/laravel-permission/issues/1156#issue-466659658
        return 'web';
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function url($locale = null): string
    {
        return '/';
    }

    public function editUrl(): string
    {
        $route = 'admin::edit-' . Str::singular($this->getTable());
        if (Route::has($route)) {
            return route($route, $this->id);
        }

        return route('admin::dashboard');
    }

    public function indexUrl(): string
    {
        $route = 'admin::index-' . $this->getTable();
        if (Route::has($route)) {
            return route($route);
        }

        return route('admin::dashboard');
    }

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($user) {
            $user->api_token = Str::uuid();
        });
    }

    public function isSuperUser(): bool
    {
        return (bool) $this->superuser;
    }

    protected function allPermissions(): Attribute
    {
        $permissions = [];
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isSuperUser()) {
                $permissions = ['all'];
            }
            foreach (Permission::all() as $permission) {
                if ($user->can($permission->name)) {
                    $permissions[] = $permission->name;
                }
            }
        }

        return Attribute::make(
            get: fn () => $permissions,
        );
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    public function isImpersonating(): bool
    {
        return Session::has('impersonation');
    }
}
