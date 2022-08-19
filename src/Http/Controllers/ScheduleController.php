<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Schedule');
    }
}
