<?php

use TypiCMS\Modules\Core\Models\Page;
use TypiCMS\Modules\Events\Models\Event;
use TypiCMS\Modules\News\Models\News;

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

test('news index returns 200', function () {
    $this->get('/en/news')->assertOk();
});

test('events index returns 200', function () {
    $this->get('/en/events/past')->assertOk();
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

test('french news index returns 200', function () {
    $this->get('/fr/actualites')->assertOk();
});

test('news detail page returns 200', function () {
    $news = News::query()
        ->published()
        ->whereNotNull('slug->en')
        ->first();

    $this->assertNotNull($news, 'No published news with English slug found');
    $this->get($news->url('en'))->assertOk();
});

test('event detail page returns 200', function () {
    $event = Event::query()
        ->published()
        ->whereNotNull('slug->en')
        ->first();

    $this->assertNotNull($event, 'No published event with English slug found');
    $this->get($event->url('en'))->assertOk();
});

describe('lang switcher', function () {
    test('on a page links to the same page in another language', function () {
        $page = Page::query()
            ->published()
            ->whereUriIs('contact')
            ->firstOrFail();

        $response = $this->get($page->url('en'));
        $response->assertOk();

        $frUrl = $page->url('fr');
        $response->assertSee('hreflang="fr"', false);
        $response->assertSee($frUrl, false);
    });

    test('on a news article links to the same article in another language', function () {
        $news = News::query()
            ->published()
            ->whereNotNull('slug->en')
            ->whereNotNull('slug->fr')
            ->firstOrFail();

        $enUrl = $news->url('en');

        $this->assertNotNull($enUrl, 'News article must have an English URL');

        $response = $this->get($enUrl);
        $response->assertOk();

        $frUrl = $news->url('fr');

        $this->assertNotNull($frUrl, 'News article must have a French URL');

        $response->assertSee('hreflang="fr"', false);
        $response->assertSee($frUrl, false);
    });
});
