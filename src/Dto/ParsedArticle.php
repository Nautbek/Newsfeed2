<?php

namespace App\Dto;

use App\Entity\Feed;

final class ParsedArticle
{
    public ?ParsedFeed $feed = null;

    public ?string $guid = null;

    public ?string $url = null;

    public ?string $title = null;

    public ?string $summary = null;

    public ?\DateTimeImmutable $publishedAt = null;
}
