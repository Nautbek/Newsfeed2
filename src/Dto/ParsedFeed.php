<?php

namespace App\Dto;

use App\Entity\Source;

class ParsedFeed
{
    public ?string $url = null;

    public ?string $type = null;

    public ?string $title = null;

    public ?Source $source = null;

    public array $articles = [];
}
