<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class APIRequest
{
    private string $base;
    private string $command;
    private array $params;
    private string $apikey;

    public function __construct(string $base, string $command, array $params, string $apikey)
    {
        $this->base = $base;
        $this->command = $command;
        $this->params = $params;
        $this->apikey = $apikey;
    }

    public function getURL(): string
    {
        $this->params['apikey'] = $this->apikey;
        $query = http_build_query($this->params);
        return "{$this->base}/{$this->command}?$query";
    }
}
