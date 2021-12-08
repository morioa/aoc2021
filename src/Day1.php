<?php
namespace AoC2021;

use AoC2021\Common;
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
    /**
     * Run method executed at script start
     * @param $dataFile
     */
    public function run($dataFile)
    {
        try {
            $this->log('Started ' . (new ReflectionClass($this))->getShortName());

            $data = $this->loadData($dataFile);

            $func = ($this->partInputRequest() === 1)
                ? 'countDepthIncreases'
                : 'countDepthRangeIncreases';
            $this->$func($data);
        } catch (Exception $e) {
            $this->log($e);
            exit(1);
        }
    }

    /**
     * Count how many times the depth increases
     * @param $data
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
    }

    /**
     * Count how many times the depth increases by range
     * @param $data
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
    }
}
