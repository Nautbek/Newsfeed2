<?php

namespace App\Feed;

class FeedJsonDetector implements FeedDetectorInterface
{
    public function detect(string $body): bool
    {
        $result = false;
        $parsedBody = json_decode($body, true);

        if (is_array($parsedBody) && isset($parsedBody['version'])) {
            $result = true;
        }

        return $result;
    }
}
