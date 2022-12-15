<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Actions\PublishPost;
use Inovector\Mixpost\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class SchedulePostController extends Controller
{
    public function __invoke(Post $post, Request $request, PublishPost $publishPost): JsonResponse
    {
        if ($post->isInHistory()) {
            return response()->json('This post is in history.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($post->isScheduleProcessing()) {
            return response()->json('This post is in the process of being published.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $postNow = $request->input('postNow', false);

        if ($postNow) {
            // Add the current time + 1 minute for the `scheduled_at` field without save it into database.
            // canSchedule method require that the `scheduled_at` field is not null and not in the past.
            $post->setAttribute('scheduled_at', now()->addMinute());
        }

        if (!$post->canSchedule()) {
            return response()->json("This post cannot be scheduled!\nPick a right time to schedule.", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $post->setScheduled($postNow ? now() : null);

        return response()->json('The post has been scheduled');
    }
}
