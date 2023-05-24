<?php

use BCLibraries\LibrarianRecommender\Trainer\AlmaClient;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * ENV variables can be in one of these files or actual ENV values. Local
 * ENV files are given precedence over the base, and actual ENV values are
 * given precedence over everything.
 */
const ENV_FILE = __DIR__ . '/../.env';
const LOCAL_ENV_FILE = __DIR__ . '/../.env.local';

$env_vals = build_env_vals();

if (! isset($env_vals['ALMA_COURSES_APIKEY'])) {
    throw new Exception("No Alma courses API key set");
}

$client = new AlmaClient($env_vals['ALMA_COURSES_APIKEY']);

$offset = 0;
$courses = $client->nextCourses($offset);
while ($courses->hasMoreCourses() > 0) {
    $client->nextCourses($courses->getNextOffset());
}

/**
 * Build the array of env values
 *
 * @return array
 * @throws Exception
 */
function build_env_vals(): array
{
    $base_env = parse_ini_file(ENV_FILE);
    $local_env = file_exists(LOCAL_ENV_FILE) ? parse_ini_file(LOCAL_ENV_FILE) : [];

    $env_vals = array_merge($base_env, $local_env, $_ENV);
    if ($env_vals === false) {
        throw new \Exception("Error building ENV values");
    }
    return $env_vals;
}

