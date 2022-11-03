<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Http\Requests\SaveSettings;
use Inovector\Mixpost\Support\TimezoneList;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings', [
            'settings' => Settings::all(),
            'timezone_list' => (new TimezoneList())->splitGroup()->list(),
        ]);
    }

    public function update(SaveSettings $saveSettings): RedirectResponse
    {
        $saveSettings->handle();

        return back();
    }
}
