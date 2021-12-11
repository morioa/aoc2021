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
     * @return void
     * @throws Exception
     */
    public function log($msg)
    {
        if (is_a($msg, 'Exception')) {
            $msg = EscapeColors::fg_color(LOG_ERROR_FG_COLOR, "ERROR:") . " {$msg->getMessage()}";
        } elseif (is_array($msg)) {
            $msg = EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, "DEBUG:\n") . trim(print_r($msg, true));
        } else {
            $msg = EscapeColors::fg_color(LOG_INFO_FG_COLOR, "INFO:") . EscapeColors::fg_color(LOG_FG_COLOR," {$msg}");
        }

        $timestamp = date('r');
        print EscapeColors::fg_color(TIMESTAMP_FG_COLOR, "[{$timestamp}]") . "  {$msg}\n";
    }

    /**
     * Load data from data file
     * @param string $dataFile
     * @param string $lineDelimiter
     * @return false|string[]
     * @throws Exception
     */
    public function loadData(string $dataFile, string $lineDelimiter = "\n")
    {
        $this->isTest = (strpos(basename($dataFile), 'test') > 0);
        $testStr = ($this->isTest) ? EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, 'test ') : '';

        $this->log("Loading {$testStr}input data");
        return explode($lineDelimiter, trim(str_replace("\r\n", "\n", file_get_contents($dataFile))));
    }

    /**
     * Set class member variable for part
     * @param $part
     * @return void
     * @throws Exception
     */
    public function setPart($part)
    {
        if ($part === 0) {
            while (!($part = $this->getPartInput())) {
                // just keep trying
            };
        }

        $this->part = $part;
    }

    /**
     * Request user input for day part to run
     * @return false|int
     * @throws Exception
     */
    public function getPartInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWhich part would you like to run? [1-2] : ");
        $input = trim(fgets(STDIN));
        if (!in_array($input, [1, 2])) {
            $this->inputError("Invalid part entry: {$input}");
            return false;
        } else {
            return (int)$input;
        }
    }

    /**
     * Gracefully handle a user input error
     * @param $msg
     * @return void
     * @throws Exception
     */
    public function inputError($msg)
    {
        $func = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'];

        //print EscapeColors::fg_color(LOG_ERROR_FG_COLOR, "\n>> {$msg} [{$func}]\n");
        print EscapeColors::fg_color(LOG_ERROR_FG_COLOR, ">> {$msg}\n");

        //$this->$func();
    }

    /**
     * Compare values of expected test result to received result
     * @param string $class
     * @param int $part
     * @param $b
     * @throws Exception
     */
    public function compareResults(string $class, int $part, $b)
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
     * @return void
     * @throws Exception
     */
    public static function done()
    {
        (new self)->log('Done');
    }
}

//register_shutdown_function(['AoC2021\Common', 'done']);