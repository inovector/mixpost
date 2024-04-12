<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Builders\PostQuery;
use Inovector\Mixpost\Http\Requests\Calendar;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Http\Resources\PostResource;
use Inovector\Mixpost\Http\Resources\TagResource;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Tag;
use Inovector\Mixpost\Support\EagerLoadPostVersionsMedia;

class CalendarController extends Controller
{
    public function index(Calendar $request): Response
    {
        $request->handle();

        $posts = PostQuery::apply($request)->oldest('scheduled_at')->get();

        EagerLoadPostVersionsMedia::apply($posts);

        return Inertia::render('Calendar', [
            'accounts' => fn() => AccountResource::collection(Account::oldest()->get())->resolve(),
            'tags' => fn() => TagResource::collection(Tag::latest()->get())->resolve(),
            'posts' => fn() => PostResource::collection($posts)->additional([
                'filter' => [
                    'accounts' => Arr::map($request->get('accounts', []), 'intval')
                ]
            ]),
            'type' => $request->type(),
            'selected_date' => $request->selectedDate(),
            'filter' => [
                'keyword' => $request->get('keyword', ''),
                'status' => $request->get('status'),
                'tags' => $request->get('tags', []),
                'accounts' => $request->get('accounts', [])
            ],
        ]);
    }
}
