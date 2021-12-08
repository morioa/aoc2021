<?php

/**
 * Class Day1
 * Sonar Sweep
 * https://adventofcode.com/2021/day/1
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day1 extends Common
{
    /**
     * Run method executed at script start
     * @param $dataFile
     */
    function run($dataFile)
    {
        try {
            self::log('Started ' . __CLASS__);

            $data = $this->loadData($dataFile);

            $func = ($this->partInputRequest() === 1)
                ? 'countDepthIncreases'
                : 'countDepthRangeIncreases';
            $this->$func($data);
        } catch (Exception $e) {
            self::log($e);
            exit(1);
        }
    }

    /**
     * Count how many times the depth increases
     * @param $data
     */
    function countDepthIncreases($data)
    {
        $increaseCount = 0;
        $prevDepth = null;
        foreach ($data as $depth) {
            if (!is_null($prevDepth) && $depth > $prevDepth) {
                $increaseCount++;
            }

            $prevDepth = $depth;
        }

        self::log("Depth increased {$increaseCount} times");
    }

    /**
     * Count how many times the depth increases by range
     * @param $data
     */
    function countDepthRangeIncreases($data)
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

        self::log("Depth range increased {$increaseCount} times");
    }
}
