<?php

namespace TypiCMS\Modules\Core\Observers;

use Bkwld\Croppa\Facades\Croppa;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use TypiCMS\Modules\Core\Services\FileUploader;

class FileObserver
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * On delete, unlink files and thumbs.
     *
     * @param Model $model eloquent
     *
     * @return mixed false or void
     */
    public function deleted(Model $model)
    {
        try {
            Croppa::delete('storage/' . $model->getOriginal('path'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * On save, upload files.
     *
     * @param Model $model eloquent
     *
     * @return mixed false or void
     */
    public function creating(Model $model)
    {
        if (request()->hasFile('name')) {
            $file = $this->fileUploader->handle(request()->file('name'));
            $model->name = $file['filename'];
            $model->fill(Arr::except($file, 'filename'));
        } else {
            if ($model->type !== 'f') {
                return false;
            }
        }
    }

    /**
     * On update, update file information & deletes the old file from storage.
     *
     * @param Model $model eloquent
     *
     * @return mixed false or void
     */
    public function updating(Model $model)
    {
        if (request()->hasFile('name')) {
            Storage::delete(request()->path);
            $file = $this->fileUploader->handle(request()->file('name'));
            $model->name = $file['filename'];
            $model->fill(Arr::except($file, 'filename'));
        }
    }
}
