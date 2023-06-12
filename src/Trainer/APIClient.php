<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class APIClient
{
    private \CurlHandle $curl;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        //  Initiate curl
        $curl_handle = curl_init();
        if ($curl_handle === false) {
            throw new \Exception('Could not start cURL');
        }
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $this->curl = $curl_handle;
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * @throws \Exception
     */
    public function getDecodedJSON(APIRequest $request, float $sleep_seconds = 1): array
    {
        $result = $this->getBareJSON($request, $sleep_seconds);
        $decoded = json_decode($result, true);
        if ($decoded === null) {
            throw new \Exception("Error decoding JSON for {$request->getURL()}: $result");
        }
        return $decoded;
    }

    /**
     * @throws \Exception
     */
    public function getBareJSON(APIRequest $request, float $sleep_seconds = 1): string
    {
        curl_setopt($this->curl, CURLOPT_URL, $request->getURL());
        $result = curl_exec($this->curl);
        if ($result === false) {
            throw new \Exception("Error sending request {$request->getURL()}:" . curl_error($this->curl));
        }

        // Sleep a bit to prevent spamming Alma.
        usleep($sleep_seconds * 1000000);

        return $result;
    }
}
