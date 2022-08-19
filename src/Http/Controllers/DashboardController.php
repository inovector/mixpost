<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(): \Inertia\Response
    {
        return Inertia::render('Dashboard');
    }
}
