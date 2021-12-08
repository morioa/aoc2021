<?php

/**
 * Class Common
 * Contains common methods used by other classes that extend it
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Common
{
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
        $this->log('Loading input data');
        return explode($lineDelimiter, trim(file_get_contents($dataFile)));
    }

    /**
     * Request user input for day part to run
     * @return int
     * @throws Exception
     */
    public function partInputRequest()
    {
        print "\nWhich part would you like to run? (1-2) : ";
        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));
        if (!in_array($input, [1, 2])) {
            throw new Exception('Invalid part specified');
        }

        return (int)$input;
    }

    /**
     * Method to execute upon script shutdown
     */
    public static function done()
    {
        (new self)->log('Done');
    }
}

register_shutdown_function(['Common', 'done']);