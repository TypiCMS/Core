<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TypiCMS\Modules\Core\Services\FileUploader;

beforeEach(function () {
    Storage::fake();
});

it('uploads a jpeg file without throwing a TypeError', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $result = (new FileUploader())->handle($file);

    expect($result)
        ->toBeArray()
        ->toHaveKeys(['filesize', 'mimetype', 'extension', 'filename', 'width', 'height', 'path', 'type'])
        ->and($result['extension'])->toBe('jpg')
        ->and($result['filename'])->toBe('photo.jpg');

    Storage::assertExists($result['path']);
});

it('uploads a jpeg file with alternate extensions', function (string $extension) {
    $file = UploadedFile::fake()->image("photo.{$extension}", 50, 50);

    $result = (new FileUploader())->handle($file);

    expect($result['extension'])->toBe('jpg');
})->with(['jpeg', 'jpe']);

it('uploads a png file', function () {
    $file = UploadedFile::fake()->image('image.png', 200, 150);

    $result = (new FileUploader())->handle($file);

    expect($result['extension'])->toBe('png')
        ->and($result['filename'])->toBe('image.png');

    Storage::assertExists($result['path']);
});

it('generates a unique filename when file already exists', function () {
    $file1 = UploadedFile::fake()->image('photo.png', 10, 10);
    $file2 = UploadedFile::fake()->image('photo.png', 10, 10);

    $result1 = (new FileUploader())->handle($file1);
    $result2 = (new FileUploader())->handle($file2);

    expect($result1['filename'])->toBe('photo.png')
        ->and($result2['filename'])->toBe('photo_1.png');
});

it('slugifies the filename', function () {
    $file = UploadedFile::fake()->image('My Photo (1).png', 10, 10);

    $result = (new FileUploader())->handle($file);

    expect($result['filename'])->toBe('my-photo-1.png');
});
