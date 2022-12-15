<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Requests\SchedulePost;

class SchedulePostController extends Controller
{
    public function __invoke(SchedulePost $schedulePost): JsonResponse
    {
        $schedulePost->handle();

        return response()->json('The post has been scheduled.');
    }
}
