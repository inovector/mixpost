<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Models\Post;

class DeletePostsController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Post::whereIn('id', $request->input('posts'))->delete();

        return redirect()->route('mixpost.posts.index');
    }
}
