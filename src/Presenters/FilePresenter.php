<?php

namespace TypiCMS\Modules\Core\Presenters;

use Illuminate\Support\Facades\Storage;

class FilePresenter extends Presenter
{
    /**
     * Get the path of the image or the path to the default image.
     */
    protected function getImagePathOrDefault(): string
    {
        $imagePath = $this->entity->path ?? '';

        if (!Storage::exists($imagePath)) {
            $imagePath = $this->imgNotFound();
        }

        return $imagePath;
    }

    /**
     * Get title.
     */
    public function title(): string
    {
        if (!empty($this->entity->title)) {
            return $this->entity->title;
        }

        return $this->entity->name;
    }

    /**
     * Format file size.
     */
    public function filesize(int $precision = 0): string
    {
        $base = log($this->entity->filesize, 1024);
        $suffixes = ['', __('KB'), __('MB'), __('GB'), __('TB')];

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];
    }
}
