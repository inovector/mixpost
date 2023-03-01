<?php

use Inertia\Testing\AssertableInertia as Assert;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Tag;
use function Pest\Faker\faker;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Post;
use Carbon\Carbon;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('shows post list', function () {
    $this->actingAs(test()->user);

    $this->publishAssets();

    Account::factory()->count(3)->create();
    Tag::factory(4)->create();
    Post::factory()->count(5)->create();
    Post::factory()->state([
        'status' => PostStatus::FAILED
    ])->count(1)->create();

    $this->get(route('mixpost.posts.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Posts/Index')
            ->has('accounts', 3)
            ->has('tags', 4)
            ->has('posts.data', 6)
            ->has('filter')
            ->where('has_failed_posts', true)
        );
});

it('shows create form', function () {
    $this->actingAs(test()->user);

    $this->publishAssets();

    Account::factory()->count(3)->create();
    Tag::factory(4)->create();

    $this->get(route('mixpost.posts.create'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Posts/CreateEdit')
            ->has('accounts', 3)
            ->has('tags', 4)
            ->where('post', null)
        );
});

it('shows edit form', function () {
    $this->actingAs(test()->user);

    $this->publishAssets();

    Account::factory()->count(3)->create();
    Tag::factory(4)->create();

    $post = Post::factory()->state([
        'status' => PostStatus::DRAFT
    ])->create();

    $this->get(route('mixpost.posts.edit', ['post' => $post]))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Posts/CreateEdit')
            ->has('accounts', 3)
            ->has('tags', 4)
            ->has('post')
            ->where('post.id', $post->id)
            ->where('post.status', $post->status->name)
        );
});

it('can store a post', function () {
    $this->actingAs(test()->user);

    $response = $this->postJson(route('mixpost.posts.store'), [
        'versions' => [
            [
                'account_id' => 0,
                'is_original' => true,
                'content' => [
                    [
                        'body' => faker()->paragraph,
                        'media' => []
                    ]
                ]
            ]
        ]
    ]);

    $post = Post::first();

    $response->assertStatus(302)->assertRedirectToRoute('mixpost.posts.edit', ['post' => $post]);
});

it('can prevent unauthorized users to store a post', function () {
    $this->postJson(route('mixpost.posts.store'))->assertUnauthorized();
});

it('can show validation on store a post', function () {
    $this->actingAs(test()->user);

    $response = $this->postJson(route('mixpost.posts.store'));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['versions']);
});

it('can update a post', function () {
    $post = Post::factory()->state([
        'status' => PostStatus::DRAFT
    ])->create();

    $post->versions()->createMany([
        [
            'account_id' => 0,
            'is_original' => true,
            'content' => [
                [
                    'body' => faker()->paragraph,
                    'media' => []
                ]
            ]
        ]
    ]);

    $this->actingAs(test()->user);

    $data = [
        'date' => Carbon::now()->toDateString(),
        'time' => Carbon::now()->toTimeString('minute'),
        'versions' => [
            [
                'account_id' => 0,
                'is_original' => true,
                'content' => [
                    [
                        'body' => faker()->paragraph,
                        'media' => []
                    ]
                ]
            ]
        ]
    ];

    $this->putJson(route('mixpost.posts.update', ['post' => $post]), $data)
        ->assertStatus(204);

    $post->refresh();

    expect($post->scheduled_at)->toEqual("{$data['date']} {$data['time']}:00")
        ->and($post->versions()->first()->content[0]['body'])->toEqual($data['versions'][0]['content'][0]['body']);
});

it('can prevent unauthorized users to update a post', function () {
    $this->putJson(route('mixpost.posts.update', ['post' => 1]))->assertUnauthorized();
});

it('can show validation on update a post', function () {
    $post = Post::factory()->create();

    $this->actingAs(test()->user);

    $response = $this->putJson(route('mixpost.posts.update', ['post' => $post]));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['versions']);
});

it('can delete a post', function () {
    $post = Post::factory()->create();

    $this->actingAs(test()->user);

    $this->delete(route('mixpost.posts.delete', ['post' => $post]))->assertStatus(302);

    $this->assertModelMissing($post);
});

it('can prevent unauthorized users to delete a post', function () {
    $this->deleteJson(route('mixpost.posts.delete', ['post' => 1]))->assertUnauthorized();
});
