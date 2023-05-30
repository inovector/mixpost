<?php

namespace Inovector\Mixpost\Enums;

enum SocialProviderResponseStatus: string
{
    case OK = 'ok';

    case EXCEEDED_RATE_LIMIT = 'exceeded_rate_limit';

    case ERROR = 'error';

    case UNAUTHORIZED = 'unauthorized';

    case NO_CONTENT = 'no_content';
}
