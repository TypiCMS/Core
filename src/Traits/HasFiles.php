<?php

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use TypiCMS\Modules\Core\Models\File;

trait HasFiles
{
    public static function bootHasFiles()
    {
        static::saved(function (Model $model) {
            if (request()->has('file_ids')) {
                $model->syncIds(request()->input('file_ids'));
            }
        });
    }

    public function syncIds(?string $ids): void
    {
        $idsArray = $ids !== null ? explode(',', $ids) : [];
        $data = [];
        $position = 1;
        foreach ($idsArray as $id) {
            $data[$id] = ['position' => $position++];
        }
        $this->files()->sync($data);
    }

    public function images()
    {
        return $this->files()->where('type', 'i');
    }

    public function documents()
    {
        return $this->files()->where('type', 'd');
    }

    public function videos()
    {
        return $this->files()->where('type', 'v');
    }

    public function audios()
    {
        return $this->files()->where('type', 'a');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files', 'model_id', 'file_id')
            ->orderBy('model_has_files.position');
    }
}
