<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class CourseList
{
    /**
     * @param Course[] $courses
     * @param int $offset
     * @param bool $has_more_courses
     */
    public function __construct(readonly array $courses,
                                readonly int   $offset,
                                readonly bool  $has_more_courses)
    {
    }

    public function nextOffset(): int
    {
        $num_courses = count($this->courses);
        return ($this->has_more_courses === false) ? $this->offset : $this->offset + $num_courses + 1;
    }
}
