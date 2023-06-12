<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

/**
 * Deduplicates readings
 *
 * Reserve readings are often entered with spelling, punctuation, and spacing
 * variations in the title and creator fields. Two records that refer to the
 * same reading might have slightly different field values.
 */
class ReadingDeduper
{
    /** @var Reading[] */
    private array $readings = [];

    /**
     * Dedup one reading
     *
     * Enter a reading and get back its original version. If this is the
     * first time we've encountered this reading, the deduped version and
     * original will be the same.
     *
     * @param Reading $reading the reading to deduplicate
     * @return Reading the deduplicated reading
     */
    public function deduplicate(Reading $reading): Reading
    {
        // Normalize the record to build the hash key and check if it has
        // already been added. If the key doesn't appear in the hash of de-duped
        // records, add it.
        $key = $this->buildKey($reading);
        if (! isset($this->readings[$key])) {
            $this->readings[$key] = $reading;
        }

        return $this->readings[$key];
    }

    /**
     * Normalize a string for comparison
     *
     * This makes two strings easier to compare by lowercasing, removing punctuation,
     * collapsing spaces, and sorting the words in alphabetical order.
     *
     * @param string $string
     * @return string the normalized string
     */
    private function normalize(string $string): string
    {
        // Lowercase
        $string = mb_strtolower($string);

        // Trim start and lead spaces
        $string = trim($string);

        // Convert punctuation to spaces.
        $string = preg_replace('/\p{P}/', ' ', $string);

        // Collapse multi-spaces to single spaces.
        $string = preg_replace("/\s\s+/", ' ', $string);

        // Split strings at spaces and recombine them in alphabetical order.
        $parts = explode(' ', $string);
        sort($parts);
        return implode(' ', $parts);
    }

    /**
     * Build a unique key for a reading in the hash
     *
     * @param Reading $reading
     * @return string
     */
    private function buildKey(Reading $reading): string
    {
        $title = $this->normalize($reading->title);
        $creator = $this->normalize($reading->creator);
        $type_code = $this->normalize($reading->type_code);
        return md5("$title $creator $type_code");
    }

}
