<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inovector\Mixpost\Model\Account;
use Inovector\Mixpost\Resources\AccountResource;

class PostsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Posts/Index');
    }

    public function create()
    {
        return Inertia::render('Posts/Create', [
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve()
        ]);
    }

    public function store()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
