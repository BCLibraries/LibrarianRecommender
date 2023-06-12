<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class TrainingDatabase
{
    private \PDO $pdo;

    private const DATA_DIR = __DIR__ . '/../../data';

    public function __construct(private string $db_file_name)
    {
        $full_path = $this->dbFilePath();
        if (!file_exists($full_path)) {
            throw new \Exception("DB $full_path does not exist");
        }
        $this->pdo = new \PDO("sqlite:$full_path");
    }

    /**
     * Create a backup of the database
     *
     * @return void
     * @throws \Exception
     */
    public function backup(): void
    {
        $db_path = $this->dbFilePath();

        // Build the backup file path.
        $parts = pathinfo($db_path);
        $timestamp = date('Ymd-His');
        $backup_name = "{$parts['filename']}-$timestamp";
        $backup_name .= $parts['extension'] ? ".{$parts['extension']}" : ''; // Extension is optional
        $backup_path = self::DATA_DIR . "/$backup_name";

        // Actually do the backup
        $success = copy($db_path, $backup_path);

        if (!$success) {
            throw new \Exception("Could not backup $db_path to $backup_path");
        }
    }

    /**
     * Add a single course
     *
     * @param Course $course
     * @return void
     */
    public function addCourse(Course $course): void
    {
        $sql = <<<SQL
INSERT OR IGNORE INTO courses(id, title, code, department_id)
VALUES(?, ?, ?, ?)
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$course->id, $course->name, $course->code, $course->getDeptCode()]);

        foreach ($course->readings as $reading) {
            $this->insertReading($reading, $course->id);
        }
    }

    /**
     * Insert a single reading
     *
     * @param Reading $reading
     * @param $course_id
     * @return void
     */
    private function insertReading(Reading $reading, $course_id): void
    {
        $insert_course_sql = <<<SQL
INSERT OR IGNORE INTO readings(id, title, type_id, creator)
VALUES(?, ?, ?, ?)
SQL;
        $insert_course_stmt = $this->pdo->prepare($insert_course_sql);
        $insert_course_stmt->execute([$reading->mms_id, $reading->title, $reading->type_code, $reading->creator]);

        $link_reading_to_course_sql = <<<SQL
INSERT OR IGNORE INTO courses_readings(course_id, reading_id)
VALUES(?, ?)
SQL;
        $link_reading_stmt = $this->pdo->prepare($link_reading_to_course_sql);
        $link_reading_stmt->execute([$course_id, $reading->mms_id]);
    }

    public function getReading(string $mms_id): ?Reading
    {
        $sql = <<<SQL
SELECT title, type_id, creator, query_result
FROM readings
WHERE id = ?
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mms_id]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (count($results) === 0) {
            return null;
        }
        return $this->buildReadingFromRow($results[0]);
    }

    /**
     * Scroll through the readings table
     *
     * We don't always want to process an array containing all
     *
     * @param int $offset the reading number to start with
     * @param int $limit how big of a batch of readings to return
     * @return Reading[] the readings in the batch
     */
    public function scrollReadings(int $offset = 0, int $limit = 1000): array
    {
        $sql = <<<SQL
SELECT id, title, type_id, creator, query_result
FROM readings
ORDER BY id
LIMIT ?, ?
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$offset, $limit]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'buildReadingFromRow'], $results);
    }

    /**
     * Update an existing reading in the database
     *
     * @param Reading $reading
     * @return void
     */
    public function updateReading(Reading $reading): void
    {
        $sql = <<<SQL
UPDATE readings
SET title = ?, type_id =?, creator = ?, query_result = ?
WHERE id = ?
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $reading->title,
            $reading->type_code,
            $reading->creator,
            $reading->query_result,
            $reading->mms_id
        ]);
    }

    /**
     * Consolidate a duplicate reading record
     *
     * @param Reading $original the original reading
     * @param Reading $duplicate the duplicate to be merged
     * @return void
     * @throws \PDOException
     */
    public function mergeDuplicateReadings(Reading $original, Reading $duplicate): void
    {

        // Point any records for the reading in the courses_readings join table
        // to the new reading.
        $update_joins_sql = <<<SQL
UPDATE OR IGNORE courses_readings
SET reading_id = ?
WHERE reading_id = ?
SQL;
        $update_joins_stmt = $this->pdo->prepare($update_joins_sql);
        $update_joins_stmt->execute([$original->mms_id, $duplicate->mms_id]);

        // Delete the duplicate reading
        $delete_reading_sql = <<<SQL
DELETE FROM readings
WHERE id = ?
SQL;
        $delete_reading_stmt = $this->pdo->prepare($delete_reading_sql);
        $delete_reading_stmt->execute([$duplicate->mms_id]);

    }

    private function buildReadingFromRow(array $row): Reading
    {
        $query_result = is_string($row['query_result']) ? $row['query_result'] : '';
        return new Reading($row['id'], $row['title'], $row['type_id'], $row['creator'], $query_result);
    }

    /**
     * Get the full path of the database file
     *
     * @return string
     */
    private function dbFilePath(): string
    {
        return self::DATA_DIR . "/{$this->db_file_name}";
    }
}
