<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Support\Facades\DB;
use Inovector\Mixpost\Models\Post;

class UpdatePost extends PostFormRequest
{
    public Post $post;

    public function withValidator($validator)
    {
        $this->post = Post::findOrFail($this->route('post'));

        $validator->after(function ($validator) {
            if ($this->post->isInHistory()) {
                $validator->errors()->add('in_history', 'The post is in history.');
            }

            if ($this->post->isScheduleProcessing()) {
                $validator->errors()->add('in_history', 'This post is being published.');
            }
        });
    }

    public function handle()
    {
        return DB::transaction(function () {
            $this->post->accounts()->sync($this->input('accounts'));
            $this->post->tags()->sync($this->input('tags'));

            $this->post->versions()->delete();
            $this->post->versions()->createMany($this->input('versions'));

            return $this->post->update([
                'scheduled_at' => $this->scheduledAt() ? convertTimeToUTC($this->scheduledAt()) : null
            ]);
        });
    }
}
