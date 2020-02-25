<?php

namespace TypiCMS\Modules\Core\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * Handle the file upload. Returns the array on success, or false
     * on failure.
     *
     * @param string $path where to upload file
     *
     * @return array|bool
     */
    public function handle(UploadedFile $file, $path = 'files')
    {
        $filesize = $file->getClientSize();
        $mimetype = $file->getClientMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $fileName.'.'.$extension;
        list($width, $height) = getimagesize($file);

        $filecounter = 1;
        while (Storage::has($path.'/'.$filename)) {
            $filename = $fileName.'_'.$filecounter++.'.'.$extension;
        }
        $path = $file->storeAs($path, $filename);
        $type = Arr::get(config('file.types'), $extension, 'd');

        return compact('filesize', 'mimetype', 'extension', 'filename', 'width', 'height', 'path', 'type');
    }
}
