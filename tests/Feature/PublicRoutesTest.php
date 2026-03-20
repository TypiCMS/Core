<?php

use TypiCMS\Modules\Core\Models\Page;

beforeEach(function () {
    $this->obLevel = ob_get_level();
});

afterEach(function () {
    while (ob_get_level() > $this->obLevel) {
        ob_end_clean();
    }
});

describe('homepage', function () {
    test('redirects to default locale', function () {
        $this->get('/')->assertRedirect('/en');
    });

    test('returns 200', function () {
        $this->get('/en')->assertOk();
    });

    test('is available in all locales', function (string $locale) {
        $this->get("/{$locale}")->assertOk();
    })->with(['en', 'fr', 'nl']);
});

test('login page returns 200', function () {
    $this->get('/en/login')->assertOk();
});

test('search page returns 200', function () {
    $this->get('/en/search-results')->assertOk();
});

test('sitemap returns 200', function () {
    $this->get('/sitemap.xml')->assertOk();
});

test('admin redirects to login for guests', function () {
    $this->get('/admin')->assertRedirect();
});

test('admin dashboard redirects to login for guests', function () {
    $this->get('/admin/dashboard')->assertRedirect();
});

test('page returns 200', function () {
    $page = Page::query()
        ->published()
        ->whereNotNull('slug->en')
        ->first();

    $this->assertNotNull($page, 'No published page with English slug found');
    $this->get($page->url('en'))->assertOk();
});

describe('lang switcher', function () {
    test('on a page links to the same page in another language', function () {
        $page = Page::query()
            ->published()
            ->whereNotNull('slug->en')
            ->whereNotNull('slug->fr')
            ->firstOrFail();

        $enUrl = $page->url('en');

        $this->assertNotNull($enUrl, 'Page must have an English URL');

        $response = $this->get($enUrl);
        $response->assertOk();

        $frUrl = $page->url('fr');

        $this->assertNotNull($frUrl, 'Page must have a French URL');

        $response->assertSee('hreflang="fr"', false);
        $response->assertSee($frUrl, false);
    });
});
