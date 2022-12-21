<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Inovector\Mixpost\Builders\PostQuery;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Http\Resources\PostResource;
use Inovector\Mixpost\Http\Resources\TagResource;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Tag;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $request->merge([
            'calendar_type' => 'week',
            'date' => $request->route('date', now()->tz(Settings::get('timezone'))->toDateString())
        ]);

        $posts = PostQuery::apply($request)->get();

        return Inertia::render('Calendar', [
            'accounts' => fn() => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => fn() => TagResource::collection(Tag::latest()->get())->resolve(),
            'posts' => fn() => PostResource::collection($posts),
        ]);
    }
}
