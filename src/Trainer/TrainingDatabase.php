<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class TrainingDatabase
{
    private \PDO $pdo;

    public function __construct(string $db_full_path)
    {
        $this->pdo = new \PDO("sqlite:$db_full_path");
    }
}
