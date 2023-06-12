<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

/**
 * A single reading
 */
class Reading
{
    /**
     * @param string $mms_id
     * @param string $title
     * @param string $type_code
     * @param string $creator
     * @param string $query_result
     */
    public function __construct(readonly string $mms_id,
                                readonly string $title,
                                readonly string $type_code,
                                readonly string $creator,
                                readonly string $query_result = '')
    {
    }

    public function searchTitle(): string
    {
        // Remove anything in parens.
        $filtered = preg_replace('/\([^)]*\)/', '', $this->title);

        // Lowercase
        $filtered = mb_strtolower($filtered);

        // Trim start and lead spaces
        $filtered = trim($filtered);

        // Convert punctuation to spaces.
        $filtered = preg_replace('/\p{P}/', ' ', $filtered);

        // Collapse multi-spaces to single spaces.
        $filtered = preg_replace("/\s\s+/", ' ', $filtered);
    }

    /**
     * Build a reading using a citation from the Alma courses API
     *
     * @param array $citation_json
     * @return Reading
     */
    public static function build(array $citation_json): Reading
    {
        $metadata = $citation_json['metadata'];

        // Just use main type.
        $type = $citation_json['type'];

        // Prefer title, then article title, then journal title, then give up.
        $title = $metadata['title'] ?? $metadata['article_title'] ?? $metadata['journal_title'] ?? '';

        // Only one creator field, I think.
        $creator = $metadata['author'] ?? '';

        // What to do if no MMS? Surely this is not the best way to handle that...
        $mms = $metadata['mms_id'] ?? '';

        return new Reading($mms, $title, $type['value'], $creator, '');
    }
}
