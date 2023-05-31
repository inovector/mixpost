<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Requests\UpdateAuthUser;

class UpdateAuthUserController extends Controller
{
    public function __invoke(UpdateAuthUser $updateUser): RedirectResponse
    {
        $updateUser->handle();

        return back();
    }
}
