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
    public function handle(UploadedFile $file, $path = 'uploads')
    {
        $input = [];

        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        // Detect and transform Croppa pattern to avoid problem with Croppa::delete()
        $fileName = preg_replace('#([0-9_]+)x([0-9_]+)#', '$1-$2', $fileName);

        $input['path'] = $path;
        $input['extension'] = '.'.$file->getClientOriginalExtension();
        $input['filesize'] = $file->getClientSize();
        $input['mimetype'] = $file->getClientMimeType();
        $input['filename'] = $fileName.$input['extension'];

        $fileTypes = config('file.types');
        try {
            $input['type'] = $fileTypes[strtolower($file->getClientOriginalExtension())];
        } catch (Exception $e) {
            $input['type'] = 'd';
        }

        $filecounter = 1;
        while (file_exists($input['path'].'/'.$input['filename'])) {
            $input['filename'] = $fileName.'_'.$filecounter++.$input['extension'];
        }

        try {
            $file->move($input['path'], $input['filename']);
            list($input['width'], $input['height']) = getimagesize($input['path'].'/'.$input['filename']);

            return $input;
        } catch (FileException $e) {
            Log::error($e->getmessage());

            return false;
        }
    }
}
