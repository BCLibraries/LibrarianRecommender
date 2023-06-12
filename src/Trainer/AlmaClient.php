<?php

namespace BCLibraries\LibrarianRecommender\Trainer;

class AlmaClient
{
    const API_BASE_URL = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1';

    private APIClient $api_client;
    private string $apikey;

    public function __construct(APIClient $api_client, string $apikey)
    {
        $this->api_client = $api_client;
        $this->apikey = $apikey;
    }

    /**
     * @throws \Exception
     */
    public function nextCourses(int $offset, int $limit = 50): CourseList
    {
        $query_params = [
            'direction' => 'ASC',
            'order_by'  => 'code,section',
            'offset'    => $offset,
            'limit'     => $limit
        ];
        echo "fetching courses from $offset...";

        $request = new APIRequest(self::API_BASE_URL, 'courses', $query_params, $this->apikey);
        $result = $this->api_client->getDecodedJSON($request, 3);
        $has_more_courses = $result['total_record_count'] > $offset + $limit;

        echo $has_more_courses ? "\n" : "final fetch\n";

        $courses = [];
        foreach ($result['course'] as $course_json) {
            echo "load readings for {$course_json['name']}...\n";
            $readings = $this->loadReadingList($course_json['id']);
            $courses[] = new Course($course_json['id'], $course_json['name'], $course_json['code'], $readings);
        }
        return new CourseList($courses, $offset, $has_more_courses);
    }

    /**
     * @param string $course_id
     * @return Reading[]
     * @throws \Exception
     */
    private function loadReadingList(string $course_id): array
    {
        $readings = [];
        $query_params = [
            'view' => 'full'
        ];
        $request = new APIRequest(self::API_BASE_URL, "courses/$course_id", $query_params, $this->apikey);
        $result = $this->api_client->getDecodedJSON($request, 2);

        // Return an empty array if we can't find a reading list
        if (!isset($result['reading_lists']) || !isset($result['reading_lists']['reading_list'])) {
            return $readings;
        }

        // Build the reading list.
        foreach ($result['reading_lists']['reading_list'] as $reading_list) {

            // Skip if there are no citations for some reason.
            if (!isset($reading_list['citations']) || !isset($reading_list['citations']['citation'])) {
                continue;
            }

            // Build a reading from each citation.
            foreach ($reading_list['citations']['citation'] as $citation) {
                $reading = Reading::build($citation);
                echo "\t...built {$reading->title}\n";
                $readings[] = $reading;
            }
        }
        return $readings;
    }
}
