<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class CourseList
{
    /** @var Course[] */
    private array $courses;
    private int $offset;
    private bool $is_finished;

    /**
     * @param Course[] $courses
     * @param int $offset
     * @param bool $has_more_courses
     */
    public function __construct(array $courses, int $offset, bool $has_more_courses)
    {
        $this->courses = $courses;
        $this->offset = $offset;
        $this->is_finished = $has_more_courses;
    }

    /**
     * @return Course[]
     */
    public function getCourses(): array
    {
        return $this->courses;
    }

    public function getNextOffset(): int
    {
        $num_courses = count($this->courses);
        return ($this->is_finished === 0) ? $this->offset : $this->offset + $num_courses;
    }

    public function hasMoreCourses(): bool
    {
        return $this->is_finished;
    }


}
