<?php

namespace TypiCMS\Modules\Core\Observers;

use Croppa;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use TypiCMS\Modules\Core\Facades\FileUpload;

class FileObserver
{
    /**
     * On delete, unlink files and thumbs.
     *
     * @param Model $model eloquent
     *
     * @return mixed false or void
     */
    public function deleted(Model $model)
    {
        $filename = $model->getOriginal('name');
        if (empty($filename)) {
            return;
        }
        $file = '/uploads/'.$filename;
        try {
            Croppa::delete($file);
            File::delete(public_path().$file);
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
        if (Request::hasFile('name')) {
            // delete prev image
            $file = FileUpload::handle(Request::file('name'), 'uploads');
            $model->name = $file['filename'];
            $model->fill(array_except($file, 'filename'));
        } else {
            if ($model->type !== 'f') {
                return false;
            }
        }
    }
}
