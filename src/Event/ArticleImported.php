<?php

namespace App\Event;

use App\Entity\Article;

/**
 * Это dto, а не сервис. Тут логики нам не надо.
 * */
final readonly class ArticleImported
{
    public function __construct(public readonly Article $article)
    {

    }
}
