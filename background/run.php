<?php
/**
 * Background script to execute AoC 2021 challenges
 * https://adventofcode.com/
 * @author Andrew Morio <morioa@hotmail.com>
 */

namespace AoC2021;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use Bart\EscapeColors;
use Exception;

/**
 * Class Run
 */
class Run
{
    protected string $srcPath;
    protected string $assetsPath;
    protected array $days;
    protected string $daysRange;
    protected string $input;

    /**
     * Run constructor
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize class member variables
     */
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

    /**
     * Begin the application
     */
    public function start()
    {
        try {
            // Prepare day range output for user input
            $daysCount = count($this->days);
            if ($daysCount === 0) {
                throw new Exception('No days available to run');
            } elseif ($daysCount === 1) {
                $this->daysRange = $this->days[0];
            } else {
                $this->daysRange = "{$this->days[0]}-{$this->days[$daysCount - 1]}";
            }

            // Get user input for day to run
            $this->getDayInput();

            // Check existence of day source file
            $dayBaseClass = "Day{$this->input}";
            $dayClass = "AoC2021\\{$dayBaseClass}";
            $dayFile = $this->srcPath . "{$dayBaseClass}.php";
            if (!file_exists($dayFile)) {
                print "\nMissing source file: \"{$dayFile}\"";
                throw new Exception('Target source file does not exist');
            }

            // Get user input for test data
            $this->getTestInput();

            // Check existence of day data file
            $dayDataFile = $this->assetsPath . "{$dayBaseClass}_{$this->input}input.txt";
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

    /**
     * Request day value from user
     * @throws Exception
     */
    function getDayInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWhich day would you like to run? ({$this->daysRange}) : ");
        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));
        if (!in_array($input, $this->days)) {
            $this->inputError('Invalid day specified');
        } else {
            $this->input = $input;
        }
    }

    /**
     * Request test value from user
     * @throws Exception
     */
    function getTestInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nDo you want to use test data? (y/n) : ");
        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));

        switch ($input) {
            case 'y':
            case 'yes':
                print EscapeColors::fg_color('yellow', ">> Using test data\n");
                $this->input = 'test';
                break;

            case 'n':
            case 'no':
                print EscapeColors::fg_color('yellow', ">> Using real data\n");
                $this->input = '';
                break;

            default:
                $this->inputError('Invalid entry');
        }
    }

    /**
     * Get restart value from user
     * @throws Exception
     */
    function getRestartInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWould you like to run again? (y/n) : ");
        $handle = fopen('php://stdin', 'r');
        $input = strtolower(trim(fgets($handle)));

        switch ($input) {
            case 'y':
            case 'yes':
                $this->start();
                break;

            case 'n':
            case 'no':
                exit(0);

            default:
                $this->inputError('Invalid entry');
        }
    }

    /**
     * Gracefully handle a user input error
     * @param $msg
     * @param bool $requestAgain
     * @throws Exception
     */
    public function inputError($msg, $requestAgain = true)
    {
        $func = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'];

        print EscapeColors::fg_color('bold_red', "\n>> {$msg} [{$func}]\n");

        if (!$requestAgain) {
            exit(1);
        }

        $this->$func();
    }
}

$run = new Run();
$run->start();
