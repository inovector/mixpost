<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Models\Post;

class UpdatePost extends PostFormRequest
{
    public function handle()
    {
        return DB::transaction(function () {
            $record = Post::findOrFail($this->route('post'));

            $record->accounts()->sync($this->input('accounts'));
            $record->tags()->sync($this->input('tags'));

            $record->versions()->delete();
            $record->versions()->createMany($this->input('versions'));

            $scheduledAt = $this->input('date') && $this->input('time') ? "{$this->input('date')} {$this->input('time')}" : null;

            return $record->update([
                'scheduled_at' => $scheduledAt ? convertTimeToUTC($scheduledAt) : null
            ]);
        });
    }
}
