<?php

namespace App\Feed;

enum FeedType: string
{
    case Rss = 'rss';

    case Atom = 'atom';

    case JsonFeed = 'json';
}
