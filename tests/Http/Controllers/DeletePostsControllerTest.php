<?php

use Illuminate\Support\Str;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Models\Post;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it("will delete posts and redirect back", function () {
    $this->actingAs(test()->user);

    $posts = Post::factory()->count(5)->create();

    $countPostsBeforeDelete = Post::count();

    $this->delete(route('mixpost.posts.multipleDelete'), [
        'posts' => $posts->pluck('id')
    ])->assertRedirect(url('/'));

    $countPostsAfterDelete = Post::count();

    expect($countPostsBeforeDelete)->toBe(5)
        ->and($countPostsAfterDelete)->toBe(0);
});

it("will delete posts and redirect to posts page", function () {
    $this->actingAs(test()->user);

    $posts = Post::factory()->count(5)->create();

    $countPostsBeforeDelete = Post::count();

    $this->delete(route('mixpost.posts.multipleDelete'), [
        'posts' => $posts->pluck('id'),
        'status' => Str::lower(PostStatus::FAILED->name)
    ])->assertRedirectToRoute('mixpost.posts.index');

    $countPostsAfterDelete = Post::count();

    expect($countPostsBeforeDelete)->toBe(5)
        ->and($countPostsAfterDelete)->toBe(0);
});
