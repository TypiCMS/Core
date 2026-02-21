<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasSlugScope
{
    /** @param Builder<Model> $query */
    #[Scope]
    protected function whereSlugIs(Builder $query, string $slug): void
    {
        $field = 'slug';
        if (in_array($field, $this->translatable ?? [], true)) {
            $field .= '->' . app()->getLocale();
        }

        $query->where($field, $slug);
    }
}
