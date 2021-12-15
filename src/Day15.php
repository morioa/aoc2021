<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day15
 * Chiton
 * https://adventofcode.com/2021/day/15
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day15 extends Common
{
    const PART_1_TEST_RESULT = 40;
    const PART_2_TEST_RESULT = 0;

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
            //$this->findPath();
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
        $this->data = $data;
        $this->log(['data' => $this->data]);

        $this->setPart($part);
    }

    public function findPath()
    {


        $calc = 0; // sum of path with lowest risk

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $calc);
        }
    }
}