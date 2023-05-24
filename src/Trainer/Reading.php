<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class Reading
{

    private string $mms_id;
    private string $title;
    private string $type_code;
    private string $type_name;
    private string $creator;

    public function __construct(string $mms_id, string $title, string $type_code, string $type_name, string $creator)
    {
        $this->title = $title;
        $this->type_code = $type_code;
        $this->type_name = $type_name;
        $this->creator = $creator;
        $this->mms_id = $mms_id;
    }

    /**
     * Build a reading using a citation from the Alma courses API
     *
     * @param array $citation_json
     * @return Reading
     */
    public static function build(array $citation_json): Reading
    {
        // Just use main type.
        $type = $citation_json['type'];

        // Prefer title, then article title, then journal title, then give up.
        $title = $metadata['title'] ?? $metadata['article_title'] ?? $metadata['journal_title'] ?? '';

        // Only one creator field, I think.
        $creator = $metadata['author'] ?? '';

        return new Reading($title, $type['value'], $type['desc'], $creator);
    }

    public function getMMS(): string {
        return $this->mms_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTypeCode(): string
    {
        return $this->type_code;
    }

    public function getTypeName(): string
    {
        return $this->type_name;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }
}
