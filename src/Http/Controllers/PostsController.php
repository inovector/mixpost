<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use \Illuminate\Http\Response as HttpResponse;
use Inovector\Mixpost\Http\Requests\StorePost;
use Inovector\Mixpost\Http\Requests\UpdatePost;
use Inovector\Mixpost\Model\Account;
use Inovector\Mixpost\Model\Post;
use Inovector\Mixpost\Model\Tag;
use Inovector\Mixpost\Resources\AccountResource;
use Inovector\Mixpost\Resources\PostResource;
use Inovector\Mixpost\Resources\TagResource;

class PostsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Posts/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Posts/CreateEdit', [
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::all())->resolve(),
            'post' => null,
        ]);
    }

    public function store(StorePost $storePost)
    {
        $post = $storePost->handle();

        return redirect()->route('mixpost.posts.edit', ['post' => $post->id]);
    }

    public function edit(Post $post): Response
    {
        $post->load('versions', 'tags');

        return Inertia::render('Posts/CreateEdit', [
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::all())->resolve(),
            'post' => new PostResource($post)
        ]);
    }

    public function update(UpdatePost $updatePost): HttpResponse
    {
        $updatePost->handle();

        return response()->noContent();
    }

    public function destroy($id): RedirectResponse
    {
        Post::where('id', $id)->delete();

        return redirect()->route('mixpost.posts.index');
    }
}
