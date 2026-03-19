<?php

beforeEach(function () {
    $this->obLevel = ob_get_level();
});

afterEach(function () {
    while (ob_get_level() > $this->obLevel) {
        ob_end_clean();
    }
});

test('regular browser requests receive HTML responses', function () {
    $this->get('/en')->assertOk()->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('requests with Accept text/markdown header receive markdown', function () {
    $this->get('/en', ['Accept' => 'text/markdown'])->assertOk()->assertHeader(
        'Content-Type',
        'text/markdown; charset=UTF-8',
    );
});

test('requests from GPTBot user agent receive markdown', function () {
    $this->get('/en', [
        'User-Agent' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.0; +https://openai.com/gptbot)',
    ])->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
});

test('requests from ClaudeBot user agent receive markdown', function () {
    $this->get('/en', [
        'User-Agent' => 'ClaudeBot/1.0',
    ])->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
});

test('requests to .md URLs receive markdown', function () {
    $this->get('/en.md')->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
});

test('admin routes are not affected by markdown middleware', function () {
    $this->get('/admin', ['Accept' => 'text/markdown'])->assertRedirect();
});

test('API routes are not affected by markdown middleware', function () {
    $this->get('/api/pages', ['Accept' => 'text/markdown'])->assertRedirect();
});

test('sitemap returns XML even when markdown is requested', function () {
    $response = $this->get('/sitemap.xml', ['Accept' => 'text/markdown']);

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('xml');
    expect($response->headers->get('Content-Type'))->not->toContain('text/markdown');
});
