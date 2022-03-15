<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TypiCMS\Modules\Core\Filters\FilterOr;
use TypiCMS\Modules\Core\Models\Role;

class RolesApiController extends BaseApiController
{
    public function index(Request $request): LengthAwarePaginator
    {
        $data = QueryBuilder::for(Role::class)
            ->allowedSorts(['name'])
            ->allowedFilters([
                AllowedFilter::custom('name', new FilterOr()),
            ])
            ->paginate($request->input('per_page'));

        return $data;
    }

    public function destroy(Role $role)
    {
        $role->delete();
    }
}
