<?php

namespace BCLibraries\LibrarianRecommender;

use BCLibraries\LibrarianRecommender\Trainer\Reading;

class Log
{
    /*** @var resource */
    private $file;

    /**
     * @param string $file_path the full path to the log file
     * @throws \Exception
     */
    public function __construct(string $file_path)
    {
        if ($fp = fopen($file_path, 'w')) {
            $this->file = $fp;
        } else {
            throw new \Exception("Could not open $file_path");
        }
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    /**
     * Write a reading to the file, with an optional message
     *
     * @param Reading $reading the Reading to log
     * @param string|null $message a message to print on the top of the Reading
     * @return void
     */
    public function logReading(Reading $reading, string $message = null): void
    {
        $string = $message ? "$message\n" : '';
        $string .= <<<READING
{$reading->title}
{$reading->creator}
{$reading->type_code}

READING;
        fwrite($this->file, $string);
    }
}
