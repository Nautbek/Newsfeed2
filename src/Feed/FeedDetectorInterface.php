<?php

namespace App\Feed;

use App\Entity\Feed;

interface FeedDetectorInterface
{
    public function detect(string $body) : bool;
}
