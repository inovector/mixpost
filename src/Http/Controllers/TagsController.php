<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Http\Requests\StoreTag;
use Inovector\Mixpost\Http\Requests\UpdateTag;
use Inovector\Mixpost\Model\Tag;

class TagsController extends Controller
{
    public function store(StoreTag $storeTag): RedirectResponse
    {
        $storeTag->handle();

        return redirect()->back();
    }

    public function update(UpdateTag $updateTag): RedirectResponse
    {
        $updateTag->handle();

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        Tag::where('id', $id)->delete();

        return redirect()->back();
    }
}
