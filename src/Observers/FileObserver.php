<?php
namespace TypiCMS\Modules\Core\Observers;

use Croppa;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use TypiCMS\Modules\Core\Facades\FileUpload;

class FileObserver
{

    /**
     * On delete, unlink files and thumbs
     * @param  Model $model eloquent
     * @return mixed false or void
     */
    public function deleted(Model $model)
    {
        if (!$attachments = $model->attachments) {
            return;
        }

        foreach ($attachments as $fieldname) {
            $this->deleteFile($fieldname, $model);
        }
    }

    /**
     * Delete file and thumbs
     *
     * @param  string $fieldname
     * @param  Model $model
     * @return void
     */
    private function deleteFile($fieldname, Model $model)
    {
        $filename = $model->getOriginal($fieldname);
        if (empty($filename)) {
            return;
        }
        $file = '/uploads/' . $model->getTable() . '/' . $filename;
        try {
            Croppa::delete($file);
            File::delete(public_path() . $file);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * On save, upload files
     *
     * @param  Model $model eloquent
     * @return mixed false or void
     */
    public function saving(Model $model)
    {

        if (!$attachments = $model->attachments) {
            return;
        }

        foreach ($attachments as $fieldname) {
            if (Input::hasFile($fieldname)) {
                // delete prev image
                $file = FileUpload::handle(Input::file($fieldname), 'uploads/' . $model->getTable());
                $model->$fieldname = $file['filename'];
                if ($model->getTable() == 'files') {
                    $model->fill($file);
                }
            } else {
                if ($model->$fieldname == 'delete') {
                    $model->$fieldname = null;
                } else {
                    $model->$fieldname = $model->getOriginal($fieldname);
                }
            }
        }
    }

    /**
     * On update, delete previous file if changed
     *
     * @param  Model $model eloquent
     * @return mixed false or void
     */
    public function updated(Model $model)
    {
        if (!$attachments = $model->attachments) {
            return;
        }

        foreach ($attachments as $fieldname) {

            // Nothing to do if file did not change
            if (!$model->isDirty($fieldname)) {
                continue;
            }
            $this->deleteFile($fieldname, $model);

        }
    }
}
