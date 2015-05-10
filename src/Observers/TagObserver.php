<?php
namespace TypiCMS\Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;
use Input;
use Log;
use Tags;

class TagObserver
{

    /**
     * On save, process tags
     *
     * @param  Model $model eloquent
     * @return mixed false or void
     */
    public function saved(Model $model)
    {
        $tags = $this->processTags(Input::get('tags'));
        $this->syncTags($model, $tags);
    }

    /**
     * Convert string of tags to array
     *
     * @param  string
     * @return array
     */
    protected function processTags($tags)
    {
        if (! $tags) {
            return [];
        }

        $tags = explode(',', $tags);

        foreach ($tags as $key => $tag) {
            $tags[$key] = trim($tag);
        }

        return $tags;
    }

    /**
     * Sync tags for model
     *
     * @param  Model $model
     * @param  array $tags
     * @return void
     */
    protected function syncTags(Model $model, array $tags)
    {
        if (! method_exists($model, 'tags')) {
            Log::info('Model doesn’t have a method called tags');
            return false;
        }

        // Create or add tags
        $tagIds = [];

        if ($tags) {
            $found = Tags::findOrCreate($tags);
            foreach ($found as $tag) {
                $tagIds[] = $tag->id;
            }
        }

        // Assign tags to model
        $model->tags()->sync($tagIds);
    }

}
