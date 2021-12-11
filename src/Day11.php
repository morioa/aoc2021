<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day11
 * Dumbo Octopus
 * https://adventofcode.com/2021/day/11
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day11 extends Common
{
    const PART_1_TEST_RESULT = 1656;
    const PART_2_TEST_RESULT = 0;
    const PART_1_STEPS = 100;
    const PART_2_STEPS = 0;

    protected array $data;

    /**
     * Run method executed at script start
     * @param $dataFile
     * @param $part
     * @return void
     * @throws Exception
     */
    public function run($dataFile, $part = null)
    {
        try {
            $this->log('Started ' . (new ReflectionClass($this))->getShortName());
            $this->init($part, $this->loadData($dataFile));

            $this->log("Executing part {$this->part}");
        } catch (Exception $e) {
            $this->log($e);
            exit(1);
        }
    }

    /**
     * Initialize data into class member variables
     * @param $part
     * @param $data
     * @return void
     * @throws Exception
     */
    public function init($part, $data)
    {
        $this->data = array_map('str_split', $data);
        $this->log(['data' => $this->data]);

        $this->setPart($part);
    }
}