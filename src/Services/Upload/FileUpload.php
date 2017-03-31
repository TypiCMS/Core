<?php

namespace TypiCMS\Modules\Core\Services\Upload;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * FileUpload.
 */
class FileUpload
{
    /**
     * Handle the file upload. Returns the array on success, or false
     * on failure.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string                                              $path where to upload file
     *
     * @return array|bool
     */
    public function handle(UploadedFile $file, $path = 'public/files')
    {
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        $fileInfo = [];
        $fileInfo['filesize'] = $file->getClientSize();
        $fileInfo['mimetype'] = $file->getClientMimeType();
        $fileInfo['extension'] = $file->getClientOriginalExtension();
        $fileInfo['filename'] = $fileName.'.'.$fileInfo['extension'];
        list($fileInfo['width'], $fileInfo['height']) = getimagesize($file);

        $filecounter = 1;
        while (Storage::has($path.'/'.$fileInfo['filename'])) {
            $fileInfo['filename'] = $fileName.'_'.$filecounter++.'.'.$fileInfo['extension'];
        }
        $fileInfo['path'] = $file->storeAs($path, $fileInfo['filename']);
        $fileInfo['filename'] = pathinfo($fileInfo['path'], PATHINFO_BASENAME);

        try {
            $fileInfo['type'] = config('file.types')[$fileInfo['extension']];
        } catch (Exception $e) {
            $fileInfo['type'] = 'd';
        }

        return $fileInfo;
    }
}
