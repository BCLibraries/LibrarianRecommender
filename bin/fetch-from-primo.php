<?php

use BCLibraries\LibrarianRecommender\Environment;
use BCLibraries\LibrarianRecommender\Trainer\APIClient;
use BCLibraries\LibrarianRecommender\Trainer\PrimoClient;
use BCLibraries\LibrarianRecommender\Trainer\Reading;
use BCLibraries\LibrarianRecommender\Trainer\TrainingDatabase;

require_once __DIR__ . '/../vendor/autoload.php';

$env_values = Environment::load();

$primo = new PrimoClient(new APIClient(), $env_values['PRIMO_APIKEY']);
$db = new TrainingDatabase($env_values['TRAINING_DB_NAME']);

$readings = $db->scrollReadings();
while (count($readings) > 0) {
    echo "fetched";
    foreach ($readings as $reading) {
        $query_result = $primo->fetchBareResponse($reading->title);
        $updated_reading = new Reading(
            $reading->mms_id,
            $reading->title,
            $reading->type_code,
            $reading->creator,
            $query_result
        );
        $db->updateReading($updated_reading);
        echo ".";
    }
    $count = count($readings);
    echo "$count\n";
    $readings = $db->scrollReadings(count($readings));
}
