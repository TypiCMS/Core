<?php

namespace TypiCMS\Modules\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    /** @return array<string, mixed> */
    public function handle(UploadedFile $file, string $path = 'files', ?string $disk = null, ?string $filenameWithoutExtension = null): array
    {
        $disk ??= config('filesystems.default');
        $extension = $this->normalizeExtension($file);
        $filesize = $file->getSize();
        $mimetype = $file->getClientMimeType();

        $filenameWithoutExtension ??= pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filenameWithoutExtension = $this->removeCroppaPattern($filenameWithoutExtension);
        $filenameWithoutExtension = Str::slug($filenameWithoutExtension) ?: Str::slug(Str::random());

        [$width, $height] = $this->getImageDimensions($file, $extension);
        $filename = $this->generateUniqueFilename($filenameWithoutExtension, $extension, $path, $disk);
        $path = $file->storeAs($path, $filename, $disk);
        $type = Arr::get(config('file.types'), $extension, 'd');

        return [
            'filesize' => $filesize,
            'mimetype' => $mimetype,
            'extension' => $extension,
            'filename' => $filename,
            'width' => $width,
            'height' => $height,
            'path' => $path,
            'type' => $type,
        ];
    }

    private function normalizeExtension(UploadedFile $file): string
    {
        $extension = mb_strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'jpe'])) {
            $extension = 'jpg';
            $this->correctImageOrientation($file);
        }

        return $extension;
    }

    private function removeCroppaPattern(string $filename): string
    {
        // Remove Croppa pattern suffixes (e.g., -200x300-resize-crop(arg1,arg2), -824x_, -_x600)
        $filename = preg_replace('/-[0-9_]+x[0-9_]+(-[0-9a-zA-Z(),._-]+)*$/', '', $filename);

        // Remove trailing underscores or hyphens
        return mb_rtrim($filename, '_-');
    }

    /** @return array{0: float|int, 1: float|int} */
    private function getImageDimensions(UploadedFile $file, string $extension): array
    {
        if ($extension === 'svg') {
            return $this->getSvgDimensions($file);
        }

        $dimensions = getimagesize($file);

        return $dimensions !== false ? [$dimensions[0], $dimensions[1]] : [0, 0];
    }

    /** @return array{0: float, 1: float} */
    private function getSvgDimensions(UploadedFile $file): array
    {
        preg_match("#viewbox=[\"']\d* \d* (\d*) (\d*)#i", $file->getContent(), $matches);

        return [
            (float) ($matches[1] ?? 0),
            (float) ($matches[2] ?? 0),
        ];
    }

    private function generateUniqueFilename(string $filenameWithoutExtension, string $extension, string $path, string $disk): string
    {
        $filename = "$filenameWithoutExtension.$extension";
        $counter = 1;

        while (Storage::disk($disk)->exists("$path/$filename")) {
            $filename = "{$filenameWithoutExtension}_{$counter}.$extension";
            $counter++;
        }

        return $filename;
    }

    private function correctImageOrientation(UploadedFile $file): void
    {
        if (!function_exists('exif_read_data')) {
            return;
        }
        $exif = @exif_read_data($file);
        if ($exif === [] || $exif === false || !isset($exif['Orientation'])) {
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
            if ($deg && $img) {
                $img = imagerotate($img, $deg, 0);
            }
            if ($img) {
                imagejpeg($img, $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(), 100);
            }
        }
    }
}
