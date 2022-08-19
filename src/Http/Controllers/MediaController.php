<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Media');
    }
}
