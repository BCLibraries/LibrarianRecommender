<?php

use BCLibraries\LibrarianRecommender\Environment;
use BCLibraries\LibrarianRecommender\Trainer\AlmaClient;
use BCLibraries\LibrarianRecommender\Trainer\APIClient;
use BCLibraries\LibrarianRecommender\Trainer\TrainingDatabase;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ENV variables can be in one of these files or actual ENV values. Local
 * ENV files are given precedence over the base, and actual ENV values are
 * given precedence over everything.
 */
$env_vals = Environment::load();

if (!isset($env_vals['TRAINING_DB_NAME'])) {
    throw new Exception("No tranining DB found");
}

$full_db_path = __DIR__ . "/../data/{$env_vals['TRAINING_DB_NAME']}";
$db = new TrainingDatabase($full_db_path);

if (!isset($env_vals['ALMA_COURSES_APIKEY'])) {
    throw new Exception("No Alma courses API key set");
}

$api_client = new APIClient();
$alma_client = new AlmaClient($api_client, $env_vals['ALMA_COURSES_APIKEY']);

$offset = 15099;
$courses = $alma_client->nextCourses($offset);
while ($courses->has_more_courses) {
    echo "Writing...\n";
    foreach ($courses->courses as $course) {
        echo "\tadding {$course->code} to database\n";
        $db->addCourse($course);
    }
    $courses = $alma_client->nextCourses($courses->nextOffset());
}

