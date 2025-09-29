<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
        $url = (string) config('typicms.welcome_message_url');
        if ($url !== '') {
            try {
                $response = $client->get($url, ['timeout' => 2]);
                if ($response->getStatusCode() < 400) {
                    $welcomeMessage = $response->getBody();
                }
            } catch (GuzzleException $exception) {
                info($exception->getMessage());
            }
        }

        return view('dashboard::show')->with(['welcomeMessage' => $welcomeMessage]);
    }
}
