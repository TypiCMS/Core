<?php

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use TypiCMS\Modules\Core\Models\File;

trait HasFiles
{
    public static function bootHasFiles(): void
    {
        static::saved(function ($model) {
            if (request()->has('file_ids')) {
                $model->syncIds(request()->string('file_ids'));
            }
        });
    }

    public function syncIds(string $ids): void
    {
        $idsArray = array_filter(array_map('trim', explode(',', $ids)));
        $data = [];
        $position = 1;
        foreach ($idsArray as $id) {
            $data[$id] = ['position' => $position++];
        }
        $this->files()->sync($data);
    }

    /** @return MorphToMany<File, $this> */
    public function images(): MorphToMany
    {
        return $this->files()->where('type', 'i');
    }

    public function documents(): MorphToMany
    {
        return $this->files()->where('type', 'd');
    }

    public function videos(): MorphToMany
    {
        return $this->files()->where('type', 'v');
    }

    public function audios(): MorphToMany
    {
        return $this->files()->where('type', 'a');
    }

    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files', 'model_id', 'file_id')
            ->orderBy('model_has_files.position');
    }
}
