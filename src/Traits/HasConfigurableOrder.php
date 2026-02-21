<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasConfigurableOrder
{
    /** @param Builder<Model> $query */
    #[Scope]
    protected function order(Builder $query): void
    {
        if ($order = config('typicms.modules.' . $this->getTable() . '.order')) {
            foreach ($order as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }
    }
}
