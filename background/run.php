<?php
/**
 * Background script to execute AoC 2021 challenges
 * https://adventofcode.com/
 * @author Andrew Morio <morioa@hotmail.com>
 */

namespace AoC2021;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use AoC2021\EscapeColors;
use Exception;

/**
 * Class Run
 */
class Run
{
    protected string $srcPath;
    protected string $assetsPath;
    protected array $days;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {

        // Set paths
        $this->srcPath = realpath(__DIR__ . '/../src/') . DIRECTORY_SEPARATOR;
        $this->assetsPath = realpath(__DIR__ . '/../assets/') . DIRECTORY_SEPARATOR;

        // Get a listing of valid days
        $this->days = array_map(function($filepath){
            preg_match('/Day(?<day>\d+)\.php$/', $filepath, $match);
            return $match['day'];
        }, glob($this->srcPath . 'Day*.php'));
        sort($this->days, SORT_NUMERIC);
    }

    public function start()
    {
        try {
            // Prepare day range output for user input
            $daysCount = count($this->days);
            if ($daysCount === 0) {
                throw new Exception('No days available to run');
            } elseif ($daysCount === 1) {
                $daysRange = $this->days[0];
            } else {
                $daysRange = "{$this->days[0]}-{$this->days[$daysCount - 1]}";
            }

            // Get user input for day to run
            $input = $this->getDayInput($daysRange);

            // Check existence of day source file
            $dayBaseClass = "Day{$input}";
            $dayClass = "AoC2021\\{$dayBaseClass}";
            $dayFile = $this->srcPath . "{$dayBaseClass}.php";
            if (!file_exists($dayFile)) {
                print "\nMissing source file: \"{$dayFile}\"";
                throw new Exception('Target source file does not exist');
            }

            // Check existence of day data file
            $dayDataFile = $this->assetsPath . "{$dayBaseClass}_input.txt";
            if (!file_exists($dayDataFile)) {
                print "\nMissing data file: \"{$dayDataFile}\"";
                throw new Exception('Target data file does not exist');
            }

            // Include day source file and run
            require_once $dayFile;
            $day = new $dayClass;
            $day->run($dayDataFile);

            // Get user input for restart
            $this->getRestartInput();
        } catch (Exception $e) {
            die("\nException caught: {$e->getMessage()}\n");
        }
    }

    function getDayInput($daysRange)
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWhich day would you like to run? ({$daysRange}) : ");
        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));
        if (!in_array($input, $this->days)) {
            throw new Exception('Invalid day specified');
        }

        return $input;
    }

    function getRestartInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWould you like to run again? (y/n) : ");
        $handle = fopen('php://stdin', 'r');
        $input = strtolower(trim(fgets($handle)));
        if (!in_array($input, ['y','yes','n','no'])) {
            throw new Exception('Invalid response');
        }

        switch ($input) {
            case 'y':
            case 'yes':
                $this->start();
                break;

            default:
                exit(0);
        }
    }
}

$run = new Run();
$run->start();
