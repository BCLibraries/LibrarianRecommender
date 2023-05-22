<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class Reading
{
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
