<?php

use Illuminate\Support\Facades\Queue;
use Inovector\Mixpost\Models\ImportedPost;
use Inovector\Mixpost\Commands\ProcessMetrics;
use Inovector\Mixpost\Models\Metric;

it('will process metrics for service providers', function () {
    Queue::fake();

    ImportedPost::factory()->count(10)->create();

    $this->artisan(ProcessMetrics::class)->assertExitCode(0);

    $this->processQueuedJobs();

    expect(Metric::count() > 0)->toBeTrue();
});
