<?php

namespace TypiCMS\Modules\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class FileUploader
{
    /**
     * Handle the file upload. Returns the array on success, or false
     * on failure.
     *
     * @param string $path where to upload file
     * @param mixed  $disk
     *
     * @return array|bool
     */
    public function handle(UploadedFile $file, $path = 'files', $disk = null)
    {
        if ($disk === null) {
            $disk = config('filesystems.default');
        }
        $filesize = $file->getSize();
        $mimetype = $file->getClientMimeType();
        $extension = mb_strtolower($file->getClientOriginalExtension());
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $fileName.'.'.$extension;
        list($width, $height) = getimagesize($file);

        $filecounter = 1;
        while (Storage::disk($disk)->has($path.'/'.$filename)) {
            $filename = $fileName.'_'.$filecounter++.'.'.$extension;
        }
        $path = $file->storeAs($path, $filename, $disk);
        $type = Arr::get(config('file.types'), $extension, 'd');

        return compact('filesize', 'mimetype', 'extension', 'filename', 'width', 'height', 'path', 'type');
    }
}
