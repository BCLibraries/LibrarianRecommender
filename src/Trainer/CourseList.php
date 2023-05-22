<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class CourseList
{
    /** @var Course[] */
    private array $courses;
    private int $offset;
    private int $remaining;

    /**
     * @param Course[] $courses
     * @param int $offset
     * @param int $remaining
     */
    public function __construct(array $courses, int $offset, int $remaining)
    {
        $this->courses = $courses;
        $this->offset = $offset;
        $this->remaining = $remaining;
    }

    /**
     * @return Course[]
     */
    public function getCourses(): array
    {
        return $this->courses;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }


}
