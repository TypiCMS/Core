<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\User;

class UsersApiController extends BaseApiController
{
    public function index(Request $request): LengthAwarePaginator
    {
        $data = QueryBuilder::for(User::class)
            ->allowedFields([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.activated',
                'users.superuser',
                'roles.name',
            ])
            ->allowedIncludes(['roles'])
            ->allowedSorts(['first_name', 'last_name', 'email', 'superuser', 'activated'])
            ->allowedFilters([
                AllowedFilter::custom('first_name,last_name,email', new FilterOr()),
            ])
            ->paginate($request->input('per_page'));

        return $data;
    }

    public function updatePreferences(Request $request): void
    {
        $user = $request->user();
        $user->preferences = array_merge((array) $user->preferences, request()->all());
        $user->save();
    }

    public function destroy(User $user)
    {
        if (auth()->user()->id === $user->id) {
            return response()->json([
                'error' => true,
                'message' => __('The current logged in user cannot be deleted.'),
            ], 403);
        }
        if (method_exists($user, 'mollieCustomerFields')) {
            if ($user->hasRunningSubscription()) {
                return response()->json([
                    'error' => true,
                    'message' => __('The user :name can not be deleted because he has a running subscription.', ['name' => "{$user->first_name} {$user->last_name}"]),
                ], 403);
            }
        }
        $user->delete();
    }
}
