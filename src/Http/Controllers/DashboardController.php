<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Models\Account;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'accounts' => fn() => AccountResource::collection(Account::oldest()->get())->resolve()
        ]);
    }
}
