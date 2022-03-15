<?php

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Models\Tag;

trait HasTags
{
    public static function bootHasTags()
    {
        static::saved(function (Model $model) {
            if (request()->has('tags')) {
                $tags = $model->processTags(request('tags'));
                $model->syncTags($model, $tags);
            }
        });
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->orderBy('tag')
            ->withTimestamps();
    }

    /**
     * Convert string of tags to array.
     */
    protected function processTags(?string $tags): array
    {
        if (!$tags) {
            return [];
        }

        $tags = explode(',', $tags);

        foreach ($tags as $key => $tag) {
            $tags[$key] = trim($tag);
        }

        return $tags;
    }

    protected function syncTags(Model $model, array $tags)
    {
        // Create or add tags
        $tagIds = [];

        if ($tags) {
            $foundTags = Tag::whereIn('tag', $tags)->get();

            $returnTags = [];

            foreach ($foundTags as $tag) {
                $pos = array_search($tag->tag, $tags);

                // Add returned tags to array
                if ($pos !== false) {
                    $returnTags[] = $tag;
                    unset($tags[$pos]);
                }
            }

            // Add remainings tags as new
            foreach ($tags as $tag) {
                $returnTags[] = Tag::create([
                    'tag' => $tag,
                    'slug' => Str::slug($tag),
                ]);
            }

            foreach ($returnTags as $tag) {
                $tagIds[] = $tag->id;
            }
        }

        // Assign tags to model
        $model->tags()->sync($tagIds);
    }
}
