<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day1
 * Sonar Sweep
 * https://adventofcode.com/2021/day/1
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day1 extends Common
{
    const PART_1_TEST_RESULT = 7;
    const PART_2_TEST_RESULT = 5;

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
            $data = $this->loadData($dataFile);
            $this->init($part);

            $this->log("Executing part {$this->part}");
            $func = ($this->part === 1)
                ? 'countDepthIncreases'
                : 'countDepthRangeIncreases';
            $this->$func($data);
        } catch (Exception $e) {
            $this->log($e);
            exit(1);
        }
    }

    /**
     * Initialize class member variables
     * @param $part
     * @return void
     * @throws Exception
     */
    public function init($part)
    {
        $this->setPart($part);
    }

    /**
     * Count how many times the depth increases
     * @param $data
     * @throws Exception
     */
    public function countDepthIncreases($data)
    {
        $increaseCount = 0;
        $prevDepth = null;
        foreach ($data as $depth) {
            if (!is_null($prevDepth) && $depth > $prevDepth) {
                $increaseCount++;
            }

            $prevDepth = $depth;
        }

        $this->log("Depth increased {$increaseCount} times");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $increaseCount);
        }
    }

    /**
     * Count how many times the depth increases by range
     * @param $data
     * @throws Exception
     */
    public function countDepthRangeIncreases($data)
    {
        $dataCount = count($data);

        $increaseCount = 0;
        $prevRangeSum = null;
        for ($i = 0; $i <= $dataCount; $i++) {
            if (($i + 3) > $dataCount) {
                break;
            }

            $rangeSum = (int)$data[$i] + (int)$data[($i+1)] + (int)$data[($i+2)];

            if (!is_null($prevRangeSum) && $rangeSum > $prevRangeSum) {
                $increaseCount++;
            }

            $prevRangeSum = $rangeSum;
        }

        $this->log("Depth range increased {$increaseCount} times");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $increaseCount);
        }
    }
}
