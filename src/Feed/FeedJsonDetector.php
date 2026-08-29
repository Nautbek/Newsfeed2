<?php

namespace App\Feed;

class FeedJsonDetector implements FeedDetectorInterface
{
    private const string JSON_START_WITH = '{';
    private const string JSON_MANDATORY_BLOCK = '"version": "https://jsonfeed.org/version/1"';

    /**
     * @throws \Exception
     */
    public function detect(string $body): bool
    {
        $result = false;
        if (str_starts_with($body, self::JSON_START_WITH) && str_contains($body, self::JSON_MANDATORY_BLOCK)) {

            if (json_validate($body)) {
                $result = true;
            } else {
                throw new \Exception('Invalid json: ' . json_last_error_msg());
            }

        }

        return $result;
    }
}
