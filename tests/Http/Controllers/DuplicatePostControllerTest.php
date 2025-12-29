<?php

use Carbon\Carbon;
use Inovector\Mixpost\Enums\PostScheduleStatus;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Models\Post;
use Inovector\Mixpost\Models\User;

use function Pest\Faker\fake;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can duplicate a post', function () {
    $this->actingAs(test()->user);

    $post = Post::factory()->state([
        'status' => PostStatus::SCHEDULED,
        'schedule_status' => PostScheduleStatus::PENDING,
        'scheduled_at' => Carbon::now()->addDay(),
    ])->create();

    $post->versions()->createMany([
        [
            'account_id' => 0,
            'is_original' => true,
            'content' => [
                [
                    'body' => fake()->paragraph,
                    'media' => [],
                ],
            ],
        ],
    ]);

    $countPostsBeforeDuplicate = Post::count();

    $this->post(route('mixpost.posts.duplicate', ['post' => $post]))
        ->assertRedirectToRoute('mixpost.posts.index');

    $countPostsAfterDuplicate = Post::count();

    $duplicatedPost = Post::latest('id')->first();

    expect($countPostsBeforeDuplicate < $countPostsAfterDuplicate)->toBeTrue()
        ->and($duplicatedPost->status->value)->toEqual(PostStatus::DRAFT->value);
});
