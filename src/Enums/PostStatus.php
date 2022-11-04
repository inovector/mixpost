<?php

namespace Inovector\Mixpost\Enums;

enum PostStatus: int
{
    case DRAFT = 0;
    case SCHEDULED = 1;
    case PUBLISHING = 2;
    case PUBLISHED = 3;
    case ERROR = 4;
}
