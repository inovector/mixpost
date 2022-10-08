<?php

namespace Inovector\Mixpost\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hex_color' => "#$this->hex_color",
        ];
    }
}
