<?php

namespace Lao9s\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class PostsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Posts/Index');
    }

    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    public function store()
    {

    }
}
