<?php

namespace TypiCMS\Modules\Core\Presenters;

use Illuminate\Support\Facades\Storage;

class FilePresenter extends Presenter
{
    protected function getImagePathOrDefault(string $relationName): string
    {
        $imagePath = $this->entity->path ?? '';

        if (!Storage::exists($imagePath)) {
            return $this->imgNotFound();
        }

        return $imagePath;
    }

    public function title(): string
    {
        if (!empty($this->entity->title)) {
            return $this->entity->title;
        }

        return $this->entity->name;
    }

    public function filesize(int $precision = 0): string
    {
        $base = log($this->entity->filesize, 1024);
        $suffixes = ['', __('KB'), __('MB'), __('GB'), __('TB')];

        return round(1024 ** ($base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
