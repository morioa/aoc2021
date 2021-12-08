<?php

/**
 * Class Day8
 * ??
 * https://adventofcode.com/2021/day/8
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day8 extends Common
{
    /**
     * Run method executed at script start
     * @param $dataFile
     */
    function run($dataFile)
    {
        try {
            self::log('Started ' . __CLASS__);

            $this->init($this->loadData($dataFile));
        } catch (Exception $e) {
            self::log($e);
            exit(1);
        }
    }

    /**
     * Initialize data into class member variables
     * @param $data
     */
    function init($data)
    {
    }
}
