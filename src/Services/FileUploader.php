<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    /** @return array<string, mixed> */
    public function handle(
        UploadedFile $file,
        string $path = 'files',
        ?string $disk = null,
        ?string $filenameWithoutExtension = null,
    ): array {
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

        if (in_array($extension, ['jpg', 'jpeg', 'jpe'], true)) {
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
        return mb_rtrim((string) $filename, '_-');
    }

    /** @return array{0: float|int, 1: float|int} */
    private function getImageDimensions(UploadedFile $file, string $extension): array
    {
        if ($extension === 'svg') {
            return $this->getSvgDimensions($file);
        }

        $dimensions = getimagesize((string) $file);

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

    private function generateUniqueFilename(
        string $filenameWithoutExtension,
        string $extension,
        string $path,
        string $disk,
    ): string {
        $filename = sprintf('%s.%s', $filenameWithoutExtension, $extension);
        $counter = 1;

        while (Storage::disk($disk)->exists(sprintf('%s/%s', $path, $filename))) {
            $filename = sprintf('%s_%d.%s', $filenameWithoutExtension, $counter, $extension);
            $counter++;
        }

        return $filename;
    }

    private function correctImageOrientation(UploadedFile $file): void
    {
        if (!function_exists('exif_read_data')) {
            return;
        }
        // @mago-ignore lint:no-error-control-operator
        $exif = @exif_read_data($file);
        $orientation = $exif['Orientation'] ?? 1;

        if ($orientation === 1) {
            return;
        }

        $degrees = match ($orientation) {
            3 => 180,
            6 => 270,
            8 => 90,
            default => 0,
        };

        $img = imagecreatefromjpeg($file);

        if (!$img) {
            return;
        }

        if ($degrees) {
            $img = imagerotate($img, $degrees, 0);
        }

        if ($img) {
            imagejpeg($img, $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(), 100);
        }
    }
}
