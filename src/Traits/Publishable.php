<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

trait Publishable
{
    public function isPublished(?string $locale = null): bool
    {
        $locale ??= app()->getLocale();

        if (in_array('status', $this->translatable ?? [], true)) {
            return (bool) $this->translate('status', $locale);
        }

        return (bool) $this->status;
    }

    /** @param Builder<Model> $query */
    #[Scope]
    protected function published(Builder $query): void
    {
        if (
            ! auth('web')->check()
            || ! auth('web')->user()?->can('see unpublished items')
            || ! request()->boolean('preview')
        ) {
            $field = 'status';
            if (in_array($field, $this->translatable ?? [], true)) {
                $field .= '->'.app()->getLocale();
            }

            $query->where($field, '1');
        }
    }

    /** @return Attribute<string, null> */
    protected function status(): Attribute
    {
        return Attribute::make(set: function ($status) {
            if (is_array($status)) {
                return json_encode(array_map(fn ($item): int => (int) $item, $status));
            }

            return $status;
        });
    }
}
