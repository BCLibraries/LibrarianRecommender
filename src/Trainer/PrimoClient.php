<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class PrimoClient
{
    const API_BASE_URL = 'https://api-na.hosted.exlibrisgroup.com/primo/v1';

    private APIClient $api_client;
    private string $apikey;

    public function __construct(APIClient $api_client, string $apikey)
    {
        $this->api_client = $api_client;
        $this->apikey = $apikey;
    }

    public function fetchBareResponse(string $term, int $sleep_seconds = 1): string
    {
        $params = [
            'vid'    => 'bclib_new',
            'tab'    => 'bclib_only',
            'scope'  => 'bcl',
            'sort'   => 'rank',
            'lang'   => 'eng',
            'offset' => '0',
            'limit'  => '5',
            'q'      => 'any,contains,' . $term,
        ];
        $request = new APIRequest(self::API_BASE_URL, 'search', $params, $this->apikey);
        return $this->api_client->getBareJSON($request, $sleep_seconds);
    }
}
