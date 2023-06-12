<?php

use BCLibraries\LibrarianRecommender\Environment;
use BCLibraries\LibrarianRecommender\Trainer\ReadingDeduper;
use BCLibraries\LibrarianRecommender\Trainer\TrainingDatabase;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Deduplicate the readings in the training database
 *
 * The training database is built directly from the readings entered in Alma. In
 * theory readings should be shared across courses, but sometimes they aren't.
 * This means the same reading can be in the database multiple times, sometimes
 * slightly varying titles or creators (e.g. "The Merchant of Venice" vs "The
 * Merchant Of Venice").
 */

// Load values set as ENV variables or set in an .env file.
$env_vals = Environment::load();

// Log
$log = new \BCLibraries\LibrarianRecommender\Log(__DIR__ . '/../data/dedup.log');

// Connect to the training database.
if (!isset($env_vals['TRAINING_DB_NAME'])) {
    throw new Exception("No training DB name found");
}
$db = new TrainingDatabase($env_vals['TRAINING_DB_NAME']);

// Be safe.
$db->backup();

// This does the actual deduplication
$deduper = new ReadingDeduper();

// Scroll through the readings table of the DB and look for duplicates,
// keeping track of how many we find.
$duplicate_count = 0;
$total_count = 0;
$readings = $db->scrollReadings();
while (count($readings) > 0) {

    // We're scrolling in batches. Iterate through each member of the batch
    // and test it in the deduper.
    foreach ($readings as $reading) {
        $total_count++;
        $orig = $deduper->deduplicate($reading);

        // If it's a duplicate, consolidate it in the database.
        if ($orig !== $reading) {
            $log->logReading($orig, "Deduplicating reading");
            $db->mergeDuplicateReadings($orig, $reading);
            $duplicate_count++;
        }
    }
    $readings = $db->scrollReadings($total_count);
}
echo "Deduplicated $duplicate_count of $total_count readings\n";
