<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inovector\Mixpost\Actions\RedirectAfterDeletedPost;
use Inovector\Mixpost\Enums\PostStatus;

it('redirects back', function () {
    $request = Request::create('/route-url');

    $response = (new RedirectAfterDeletedPost)($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toEqual(url('/'));
});

it('redirects to posts page', function () {
    $request = Request::create('/route-url', 'GET', ['status' => Str::lower(PostStatus::FAILED->name)]);

    $response = (new RedirectAfterDeletedPost)($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toEqual(route('mixpost.posts.index'));
});
