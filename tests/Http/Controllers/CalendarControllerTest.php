<?php

use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Inovector\Mixpost\Enums\PostScheduleStatus;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Post;
use Inovector\Mixpost\Models\Tag;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();

    Account::factory()->count(3)->create();
    Tag::factory(4)->create();

    $this->publishAssets();
});

test('calendar page - Month type', function () {
    $this->actingAs(test()->user);

    Post::factory()
        ->count(2)
        ->state([
            'status' => PostStatus::SCHEDULED,
            'schedule_status' => PostScheduleStatus::PENDING,
        ])
        ->withScheduledAtBetweenDates(Carbon::now()->subMonths(3)->startOfMonth(), Carbon::now()->subMonths(3)->endOfMonth())
        ->create();

    Post::factory()
        ->count(5)
        ->state([
            'status' => PostStatus::SCHEDULED,
            'schedule_status' => PostScheduleStatus::PENDING,
        ])
        ->withScheduledAtBetweenDates(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth())
        ->create();

    $this->get(route('mixpost.calendar'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->has('accounts', 3)
            ->has('tags', 4)
            ->has('posts.data', 5)
            ->where('type', 'month')
            ->where('selectedDate', Carbon::now()->tz(Settings::get('timezone'))->toDateString())
            ->has('filter')
        );
});

test('calendar page - Week type', function () {
    $this->actingAs(test()->user);

    Post::factory()
        ->count(2)
        ->state([
            'status' => PostStatus::SCHEDULED,
            'schedule_status' => PostScheduleStatus::PENDING,
        ])
        ->withScheduledAtBetweenDates(Carbon::now()->subMonths(3)->startOfWeek(), Carbon::now()->subMonths(3)->endOfWeek())
        ->create();

    Post::factory()
        ->count(5)
        ->state([
            'status' => PostStatus::SCHEDULED,
            'schedule_status' => PostScheduleStatus::PENDING,
        ])
        ->withScheduledAtBetweenDates(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek())
        ->create();

    $today = Carbon::now()->tz(Settings::get('timezone'))->toDateString();

    $this->get(route('mixpost.calendar', ['date' => $today, 'type' => 'week']))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Calendar')
            ->has('accounts', 3)
            ->has('tags', 4)
            ->has('posts.data', 5)
            ->where('type', 'week')
            ->where('selectedDate', $today)
            ->has('filter')
        );
});
