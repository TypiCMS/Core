<?php

use Illuminate\Console\View\Components\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use TypiCMS\Modules\Core\Commands\Database;
use TypiCMS\Modules\Core\Commands\Install;

function createInstallCommand(Filesystem $filesystem): Install
{
    $command = new Install($filesystem);

    $components = mock(Factory::class);
    $components->shouldReceive('info')->andReturnNull();
    $components->shouldReceive('task')->andReturnUsing(function ($label, $callback): void {
        $callback();
    });

    new ReflectionProperty($command, 'components')->setValue($command, $components);

    return $command;
}

function callGuessDatabaseName(): string
{
    $command = resolve(Database::class);
    $method = new ReflectionMethod($command, 'guessDatabaseName');

    return $method->invoke($command);
}

test('guessDatabaseName returns a non-empty string', function (): void {
    expect(callGuessDatabaseName())->toBeString()->not->toBeEmpty();
});

test('guessDatabaseName strips the TLD extension', function (): void {
    expect(callGuessDatabaseName())->not->toContain('.');
});

test('guessDatabaseName returns a valid slug', function (): void {
    expect(callGuessDatabaseName())->toMatch('/^[a-z0-9]+(-[a-z0-9]+)*$/');
});

test('guessDatabaseName matches the project directory name without TLD', function (): void {
    $expected = Str::slug(Str::before(basename(dirname(app_path())), '.'));

    expect(callGuessDatabaseName())->toBe($expected);
});

test('setAppUrl replaces APP_URL in .env file', function (): void {
    $written = null;

    $filesystem = mock(Filesystem::class);
    $filesystem->shouldReceive('get')->with('.env')->once()->andReturn("APP_NAME=TypiCMS\nAPP_URL=http://localhost\nAPP_DEBUG=true");
    $filesystem->shouldReceive('put')->with('.env', Mockery::capture($written))->once();

    $command = createInstallCommand($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');
    $method->invoke($command, 'my-site.test');

    expect($written)
        ->toContain('APP_URL=https://my-site.test')
        ->toContain('APP_NAME=TypiCMS')
        ->toContain('APP_DEBUG=true');
});

test('setAppUrl overwrites existing APP_URL value', function (): void {
    $written = null;

    $filesystem = mock(Filesystem::class);
    $filesystem->shouldReceive('get')->with('.env')->once()->andReturn('APP_URL=https://old-site.test');
    $filesystem->shouldReceive('put')->with('.env', Mockery::capture($written))->once();

    $command = createInstallCommand($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');
    $method->invoke($command, 'new-site.test');

    expect($written)->toBe('APP_URL=https://new-site.test');
});

test('setAppUrl preserves other .env keys', function (): void {
    $written = null;

    $filesystem = mock(Filesystem::class);
    $filesystem->shouldReceive('get')->with('.env')->once()->andReturn("APP_NAME=TypiCMS\nAPP_URL=http://localhost\nAPP_URL_ADMIN=/admin");
    $filesystem->shouldReceive('put')->with('.env', Mockery::capture($written))->once();

    $command = createInstallCommand($filesystem);

    $method = new ReflectionMethod($command, 'setAppUrl');
    $method->invoke($command, 'example.test');

    expect($written)
        ->toContain('APP_URL=https://example.test')
        ->toContain('APP_NAME=TypiCMS');
});

test('command is registered as typicms:install', function (): void {
    $command = resolve(Install::class);

    expect($command->getName())->toBe('typicms:install');
});
