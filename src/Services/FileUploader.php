<?php

namespace TypiCMS\Modules\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $this->correctImageOrientation($file);
        $filesize = $file->getSize();
        $mimetype = $file->getClientMimeType();
        $extension = mb_strtolower($file->getClientOriginalExtension());
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
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

    private function correctImageOrientation(UploadedFile $file): void
    {
        if (!function_exists('exif_read_data')) {
            return;
        }
        $exif = exif_read_data($file);
        if (!$exif || !isset($exif['Orientation'])) {
            return;
        }
        $orientation = $exif['Orientation'];
        if ($orientation !== 1) {
            $img = imagecreatefromjpeg($file);
            $deg = 0;

            switch ($orientation) {
                    case 3:
                        $deg = 180;

                        break;

                    case 6:
                        $deg = 270;

                        break;

                    case 8:
                        $deg = 90;

                        break;
                }
            if ($deg) {
                $img = imagerotate($img, $deg, 0);
            }
            // then rewrite the rotated image back to the disk as $file
            imagejpeg($img, $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename(), 100);
        }
    }
}
