<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class ReachableFeed extends Constraint
{
    public string $message = '«{{ url }}: не удалось получить валидный фид (%reason%)»';
}
