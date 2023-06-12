<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class Course
{
    /**
     * @param string $id
     * @param string $name
     * @param string $code
     * @param Reading[] $readings
     */
    public function __construct(readonly string $id,
                                readonly string $name,
                                readonly string $code,
                                readonly array  $readings)
    {
    }

    public function getDeptCode(): string
    {
        $uc_code = strtoupper($this->getCode());
        if (preg_match('/^([A-Z]*)/', $uc_code, $matches)) {
            return $matches[1];
        } else {
            return '';
        }

    }
}
