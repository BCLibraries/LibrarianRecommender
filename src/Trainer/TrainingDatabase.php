<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class TrainingDatabase
{
    private \PDO $pdo;

    public function __construct(string $db_full_path)
    {
        $this->pdo = new \PDO("sqlite:$db_full_path");
    }

    public function addCoruse(Course $course): void
    {
        $sql = <<<SQL
INSERT INTO courses(id, name, code)
VALUES(:course_id, :course_name, :course_code)
SQL;

    }

    private function addReading(Reading $reading, $course_id): void
    {
        $sql = <<<SQL
INSERT INTO readings(id, title, type_id)
VALUES(:id, :title, :type_id)
SQL;
    }

    private function getReading(string $mms_id): ?Reading
    {
        $sql = <<<SQL
SELECT r.id, r.title, t.id, t.title, r.creator
FROM readings r 
LEFT JOIN types t ON type_id = t.id
WHERE id = :mms_id
SQL;
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':mms_id', $mms_id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) === 0) {
            return null;
        }

        $reading_data = $results[0];
        return new Reading($mms_id, $reading_data[0], $reading_data[1], $reading_data[2], $reading_data[3]);
    }
}
