<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class AlmaClient
{
    const API_BASE_URL = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1';

    private string $apikey;
    private \CurlHandle $curl;

    /**
     * @throws \Exception
     */
    public function __construct(string $apikey)
    {
        $this->apikey = $apikey;

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
    public function nextCourses(int $offset, int $limit = 100): CourseList
    {
        $query_params = [
            'direction' => 'ASC',
            'order_by'  => 'code,section',
            'offset'    => $offset,
            'limit'     => $limit
        ];
        $result = $this->sendRequest('courses', $query_params);
        $remaining = $result['total_record_count'] - ($offset * $limit);

        $courses = [];
        foreach ($result['course'] as $course_json) {
            $courses[] = new Course($course_json['id'], $course_json['name'], $course_json['code']);
        }
        return new CourseList($courses, $offset, $remaining);
    }

    /**
     * @throws \Exception
     */
    private function sendRequest(string $command, array $params, float $sleep_seconds = 1): array
    {
        $params['apikey'] = $this->apikey;
        $query = http_build_query($params);
        $full_url = self::API_BASE_URL . "/$command?$query";
        curl_setopt($this->curl, CURLOPT_URL, $full_url);
        $result = curl_exec($this->curl);
        if ($result === false) {
            throw new \Exception("Error sending request $full_url:" . curl_error($this->curl));
        }
        $decoded = json_decode($result, true);

        if ($decoded === null) {
            throw new \Exception("Error decoding JSON for $full_url");
        }

        // Sleep a bit to prevent spamming Alma.
        usleep($sleep_seconds * 1000000);

        return $decoded;
    }
}
