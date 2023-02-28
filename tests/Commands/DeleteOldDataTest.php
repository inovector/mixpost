<?php

use Carbon\Carbon;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\Models\FacebookInsight;
use Inovector\Mixpost\Commands\DeleteOldData;

it('will delete old data', function () {
    ImportedPost::factory()->count(10)->state([
        'created_at' => Carbon::now()->subMonths(5),
    ])->create();

    FacebookInsight::factory()->count(10)->state([
        'date' => Carbon::now()->subMonths(4),
    ])->create();

    $this->artisan(DeleteOldData::class)->assertExitCode(0);

    expect(ImportedPost::count() === 0)->toBeTrue()
        ->and(FacebookInsight::count() === 0)->toBeTrue();
});

it("won't delete date that shouldn't be deleted", function () {
    ImportedPost::factory()->count(10)->state([
        'created_at' => Carbon::now()->subDays(30),
    ])->create();

    FacebookInsight::factory()->count(10)->state([
        'date' => Carbon::now()->subDays(80),
    ])->create();

    $this->artisan(DeleteOldData::class)->assertExitCode(0);

    expect(ImportedPost::count() === 10)->toBeTrue()
        ->and(FacebookInsight::count() === 10)->toBeTrue();
});
