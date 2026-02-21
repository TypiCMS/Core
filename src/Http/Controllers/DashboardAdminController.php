<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

final class DashboardAdminController extends BaseAdminController
{
    public function index(): RedirectResponse
    {
        return redirect(route('admin::dashboard'));
    }

    public function dashboard(): View
    {
        $welcomeMessage = $this->fetchWelcomeMessage();

        return view('dashboard::show', compact('welcomeMessage'));
    }

    private function fetchWelcomeMessage(): string
    {
        $fallback = (string) config('typicms.welcome_message');
        $url = (string) config('typicms.welcome_message_url');

        if ($url === '') {
            return $fallback;
        }

        try {
            $response = Http::timeout(2)->get($url);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        return $fallback;
    }
}
