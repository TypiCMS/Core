<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Commands\Install;

test('guessDatabaseName returns a non-empty string', function () {
    $command = app(Install::class);

    expect($command->guessDatabaseName())->toBeString()->not->toBeEmpty();
});

test('guessDatabaseName strips the TLD extension', function () {
    $command = app(Install::class);

    expect($command->guessDatabaseName())->not->toContain('.');
});

test('guessDatabaseName returns a valid slug', function () {
    $command = app(Install::class);

    expect($command->guessDatabaseName())->toMatch('/^[a-z0-9]+(-[a-z0-9]+)*$/');
});

test('guessDatabaseName matches the project directory name', function () {
    $command = app(Install::class);
    $expected = Str::slug(Str::before(basename(dirname(app_path())), '.'));

    expect($command->guessDatabaseName())->toBe($expected);
});

test('setAppUrl replaces APP_URL in .env file', function () {
    $filesystem = new Filesystem();
    $envPath = sys_get_temp_dir() . '/test_env_' . uniqid();
    $filesystem->put($envPath, "APP_NAME=TypiCMS\nAPP_URL=http://localhost\nAPP_DEBUG=true");

    $command = new Install($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');

    // Temporarily override the .env path by swapping working directory
    $originalDir = getcwd();
    $tmpDir = dirname($envPath);
    $tmpEnv = $tmpDir . '/.env';
    rename($envPath, $tmpEnv);
    chdir($tmpDir);

    $method->invoke($command, 'my-site.test');

    $result = $filesystem->get($tmpEnv);
    chdir($originalDir);
    $filesystem->delete($tmpEnv);

    expect($result)
        ->toContain('APP_URL=https://my-site.test')
        ->toContain('APP_NAME=TypiCMS')
        ->toContain('APP_DEBUG=true');
});

test('setAppUrl overwrites existing APP_URL value', function () {
    $filesystem = new Filesystem();
    $tmpDir = sys_get_temp_dir();
    $tmpEnv = $tmpDir . '/.env';
    $filesystem->put($tmpEnv, "APP_URL=https://old-site.test");

    $command = new Install($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');

    $originalDir = getcwd();
    chdir($tmpDir);

    $method->invoke($command, 'new-site.test');

    $result = $filesystem->get($tmpEnv);
    chdir($originalDir);
    $filesystem->delete($tmpEnv);

    expect($result)->toBe("APP_URL=https://new-site.test");
});

test('setAppUrl preserves other .env keys', function () {
    $filesystem = new Filesystem();
    $tmpDir = sys_get_temp_dir();
    $tmpEnv = $tmpDir . '/.env';
    $filesystem->put($tmpEnv, "APP_NAME=TypiCMS\nAPP_URL=http://localhost\nAPP_URL_ADMIN=/admin");

    $command = new Install($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');

    $originalDir = getcwd();
    chdir($tmpDir);

    $method->invoke($command, 'example.test');

    $result = $filesystem->get($tmpEnv);
    chdir($originalDir);
    $filesystem->delete($tmpEnv);

    expect($result)
        ->toContain('APP_URL=https://example.test')
        ->toContain('APP_NAME=TypiCMS');
});

test('command is registered as typicms:install', function () {
    $command = app(Install::class);

    expect($command->getName())->toBe('typicms:install');
});
