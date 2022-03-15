<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardAdminController extends BaseAdminController
{
    public function index(): RedirectResponse
    {
        return redirect(route('admin::dashboard'));
    }

    public function dashboard(Client $client): View
    {
        $welcomeMessage = config('typicms.welcome_message');
        $url = config('typicms.welcome_message_url');

        try {
            $response = $client->get($url, ['timeout' => 2]);
            if ($response->getStatusCode() < 400) {
                $welcomeMessage = $response->getBody();
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        return view('dashboard::show')->with(compact('welcomeMessage'));
    }
}
