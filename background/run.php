<?php

/**
 * Background script to execute AoC 2021 challenges
 * https://adventofcode.com/
 * @author Andrew Morio <morioa@hotmail.com>
 */
require_once __DIR__ . '/../autoload.php';

try {
    // Set paths
    $srcPath = realpath(__DIR__ . '/../src/') . DIRECTORY_SEPARATOR;
    $assetsPath = realpath(__DIR__ . '/../assets/') . DIRECTORY_SEPARATOR;

    // Get a listing of valid days
    $days = array_map(function($filepath){
        preg_match('/Day(?<day>\d+)\.php$/', $filepath, $match);
        return $match['day'];
    }, glob($srcPath . 'Day*.php'));
    sort($days, SORT_NUMERIC);

    // Prepare day range output for user input
    $daysCount = count($days);
    if ($daysCount === 0) {
        throw new Exception('No days available to run');
    } elseif ($daysCount === 1) {
        $daysRange = $days[0];
    } else {
        $daysRange = "{$days[0]}-{$days[$daysCount - 1]}";
    }

    // Get user input for day to run
    print "\nWhich day would you like to run? ({$daysRange}) : ";
    $handle = fopen('php://stdin', 'r');
    $input = trim(fgets($handle));
    if (!in_array($input, $days)) {
        throw new Exception('Invalid day specified');
    }

    // Verify existence of day source file
    $dayClass = "Day{$input}";
    $dayFile = $srcPath . "{$dayClass}.php";
    if (!file_exists($dayFile)) {
        print "\nMissing source file: \"{$dayFile}\"";
        throw new Exception('Target source file does not exist');
    }

    // Verify existence of day data file
    $dayDataFile = $assetsPath . "{$dayClass}_input.txt";
    if (!file_exists($dayDataFile)) {
        print "\nMissing data file: \"{$dayDataFile}\"";
        throw new Exception('Target data file does not exist');
    }

    // Include day source file and run
    require_once $dayFile;
    $day = new $dayClass;
    $day->run($dayDataFile);
} catch (Exception $e) {
    die("\nException caught: {$e->getMessage()}\n");
}
