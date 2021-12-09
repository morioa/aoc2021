<?php
namespace AoC2021;

use Bart\EscapeColors;
use Exception;

require_once __DIR__ . '/../config/config.php';

/**
 * Class Common
 * Contains common methods used by other classes that extend it
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Common
{
    protected bool $isTest = false;
    protected int $part;

    /**
     * Output standard log message
     * @param $msg
     */
    public function log($msg)
    {
        if (is_a($msg, 'Exception')) {
            $msg = "ERROR: {$msg->getMessage()}";
        } elseif (is_array($msg)) {
            $msg = "DEBUG:\n" . trim(print_r($msg, true));
        } else {
            $msg = "INFO: {$msg}";
        }

        $timestamp = date('r');
        print "[{$timestamp}] {$msg}\n";
    }

    /**
     * Load data from data file
     * @param $dataFile
     * @param string $lineDelimiter
     * @return false|string[]
     */
    public function loadData($dataFile, $lineDelimiter = "\n")
    {
        $this->isTest = (strpos(basename($dataFile), 'test') > 0);

        $this->log('Loading input data');
        return explode($lineDelimiter, trim(str_replace("\r\n", "\n", file_get_contents($dataFile))));
    }

    /**
     * Request user input for day part to run
     * @throws Exception
     */
    public function getPartInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWhich part would you like to run? (1-2) : ");
        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));
        if (!in_array($input, [1, 2])) {
            $this->inputError('Invalid part specified');
        } else {
            $this->part = (int)$input;
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

    /**
     * Compare values of expected test result to received result
     * @param $class
     * @param $part
     * @param $b
     * @throws Exception
     */
    public function compareResults($class, $part, $b)
    {
        $results = [
            'match' => 'bold_blue',
            'differ' => 'bold_red',
        ];

        $a = constant($class . "::PART_{$part}_TEST_RESULT");
        $result = ($a === $b) ? 'match' : 'differ';

        print EscapeColors::fg_color(
            $results[$result],
            "Results {$result}\n    Expected: {$a}\n    Received: {$b}\n"
        );
    }

    /**
     * Method to execute upon script shutdown
     */
    public static function done()
    {
        (new self)->log('Done');
    }
}

register_shutdown_function(['AoC2021\Common', 'done']);