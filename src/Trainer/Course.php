<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class Course
{
    private string $id;
    /** @var Reading[] */
    private array $readings;
    private string $name;
    private string $code;

    public function __construct(string $id, string $name, string $code)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * @return Reading[]
     */
    public function getReadings(): array
    {
        return $this->readings;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function addReading(Reading $reading): void
    {
        $this->readings[] = $reading;
    }
}
