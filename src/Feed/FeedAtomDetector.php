<?php

namespace App\Feed;

class FeedAtomDetector implements FeedDetectorInterface
{
    private const string ATOM_START_BLOCK = '<feed xmlns="http://www.w3.org/2005/Atom"';
    public function detect(string $body): bool
    {
        $result = false;
        if (str_contains($body, self::ATOM_START_BLOCK)) {
            $result = true;
        }

        return $result;
    }
}
