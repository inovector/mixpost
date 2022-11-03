<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Actions\PublishPost;
use Inovector\Mixpost\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class SchedulePostController extends Controller
{
    public function __invoke(Post $post, Request $request, PublishPost $publishPost)
    {
        if ($post->isPublishing()) {
            return response()->json('This post is currently being published.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$post->scheduled_at) {
            if ($post->isPublished()) {
                return response()->json('It has already been published', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $publishPost($post);

            return response()->json('The post is being published');
        }

        if (!$post->canSchedule()) {
            return response()->json('This post cannot be scheduled! It has already been scheduled or published.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($post->scheduled_at->isPast()) {
            return response()->json('This post cannot be scheduled! The scheduled date is in the past.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $post->setScheduled();

        return response()->json('The post has been scheduled');
    }
}
