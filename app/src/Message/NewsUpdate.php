<?php

namespace App\Message;

class NewsUpdate
{
    private array $newsArray;

    public function __construct(array $newsArray)
    {
        $this->newsArray = $newsArray;
    }

    public function getNewsArray(): array
    {
        return $this->newsArray;
    }
}