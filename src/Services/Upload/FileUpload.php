<?php

namespace TypiCMS\Modules\Core\Services\Upload;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        $fileInfo = [];
        $fileInfo['original_name'] = $file->getClientOriginalName();
        $fileInfo['path'] = $file->store($path);
        $fileInfo['filesize'] = $file->getClientSize();
        $fileInfo['mimetype'] = $file->getClientMimeType();
        list($fileInfo['width'], $fileInfo['height']) = getimagesize($file);
        $fileInfo['filename'] = pathinfo($fileInfo['path'], PATHINFO_BASENAME);
        $fileInfo['extension'] = pathinfo($fileInfo['path'], PATHINFO_EXTENSION);
        try {
            $fileInfo['type'] = config('file.types')[$fileInfo['extension']];
        } catch (Exception $e) {
            $fileInfo['type'] = 'd';
        }

        return $fileInfo;
    }
}
