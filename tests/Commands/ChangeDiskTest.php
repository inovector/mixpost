<?php


use Inovector\Mixpost\Models\Media;

it('changes disks', function () {
    Media::factory()->count(10)->create();

    $this->artisan(\Inovector\Mixpost\Commands\ChangeDisk::class, [
        'from' => 'public',
        'to' => 's3',
    ])->assertExitCode(0);

    expect(Media::where('disk', 'public')->count())->toBe(0);
    expect(Media::where('disk', 's3')->count())->toBe(10);
});
