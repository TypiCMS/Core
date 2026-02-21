<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Models;

use Exception;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;
use TypiCMS\Modules\Core\Presenters\TagsModulePresenter;
use TypiCMS\Modules\Core\Traits\HasAdminUrls;
use TypiCMS\Modules\Core\Traits\HasConfigurableOrder;
use TypiCMS\Modules\Core\Traits\HasSelectableFields;
use TypiCMS\Modules\Core\Traits\HasSlugScope;
use TypiCMS\Modules\Core\Traits\Historable;
use TypiCMS\Modules\Core\Traits\Publishable;

/**
 * @property int $id
 * @property string $tag
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, History> $history
 * @property-read int|null $history_count
 * @property-write mixed $status
 */
class Tag extends Model
{
    use Cachable;
    use HasAdminUrls;
    use HasConfigurableOrder;
    use HasSelectableFields;
    use HasSlugScope;
    use Historable;
    use PresentableTrait;
    use Publishable;

    protected string $presenter = TagsModulePresenter::class;

    protected $guarded = [];

    #[Scope]
    protected function published(Builder $query): void
    {
    }

    /**
     * Get all tagged items grouped by type
     *
     * @return array<string, Collection>
     */
    public function getTaggedItemsGrouped(): array
    {
        $taggables = DB::table('taggables')->where('tag_id', $this->id)->get();

        $grouped = [];

        foreach ($taggables as $taggable) {
            $modelClass = $taggable->taggable_type;
            $modelId = $taggable->taggable_id;

            if (!class_exists($modelClass)) {
                continue;
            }

            try {
                $model = $modelClass::query()->find($modelId);
                if ($model) {
                    $type = $model->getTable();
                    if (!isset($grouped[$type])) {
                        $grouped[$type] = collect();
                    }

                    $grouped[$type]->push($model);
                }
            } catch (Exception) {
                continue;
            }
        }

        return $grouped;
    }
}
