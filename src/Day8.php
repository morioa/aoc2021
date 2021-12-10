<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day8
 * Seven Segment Search
 * https://adventofcode.com/2021/day/8
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day8 extends Common
{
    const PART_1_TEST_RESULT = 26;
    const PART_2_TEST_RESULT = 5353;

    protected array $patterns = [];
    protected array $displays = [];
    protected array $displaysBySegmentsCount = [];
    protected array $knownNumsBySegments = [];

    /**
     * Run method executed at script start
     * @param $dataFile
     */
    public function run($dataFile)
    {
        try {
            $this->log('Started ' . (new ReflectionClass($this))->getShortName());

            $this->init($this->loadData($dataFile));

            $func = ($this->part === 1)
                ? 'countKnownDigits'
                : 'decodeSignals';
            $this->$func();
        } catch (Exception $e) {
            $this->log($e);
            exit(1);
        }
    }

    /**
     * Initialize data into class member variables
     * @param $data
     * @throws Exception
     */
    public function init($data)
    {
        $this->displays = [
            0 => 'abcefg',
            1 => 'cf',
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',
            8 => 'abcdefg',
            9 => 'abcdfg',
        ];
        $this->log(['displays' => $this->displays]);

        $this->displaysBySegmentsCount = [];
        foreach ($this->displays as $num => $segments) {
            $segmentsCount = strlen($segments);
            $this->displaysBySegmentsCount[$segmentsCount][] = $num;
        }
        ksort($this->displaysBySegmentsCount);
        $this->log(['displays by segments count' => $this->displaysBySegmentsCount]);

        foreach ($this->displaysBySegmentsCount as $count => $nums) {
            $numsCount = count($nums);
            if ($numsCount === 1) {
                $this->knownNumsBySegments[$count] = $nums[0];
            }
        }
        ksort($this->knownNumsBySegments);
        $this->log(['known numbers by segments' => $this->knownNumsBySegments]);

        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
            list($l, $r) = explode(' | ', $data[$i]);
            $this->patterns[$i]['source'] = explode(' ', $l);
            $this->patterns[$i]['output'] = explode(' ', $r);
        }
        //$this->log(['patterns' => $this->patterns]);

        $this->getPartInput();
    }

    public function countKnownDigits()
    {
        $knownNumsCount = 0;
        foreach ($this->patterns as $pattern) {
            foreach ($pattern['output'] as $segment) {
                $segmentLength = strlen($segment);
                $knownNumsCount += (isset($this->knownNumsBySegments[$segmentLength]));
            }
        }
        $this->log("Known digits count: {$knownNumsCount}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $knownNumsCount);
        }
    }

    public function decodeSignals()
    {
        $posFindOrder = [
            1,  // narrow position of 'cf'
            7,  // find position of 'd' (using 1)
            4,  // narrow position of 'bd' (using 7)
            9,  // find position of 'g' [and 'e'] (using 4)
            6,  // find position of 'f' [and 'c']
            0,  // find position of 'b' [and 'd']
        ];

        foreach ($this->patterns as $pattern) {
            $sources = $pattern['source'];
            usort($sources, function($a, $b) {
                return (strlen($a) - strlen($b));
            });
            $this->log(['source' => $sources]);

            $sourceNums = [];
            foreach ($sources as $source) {

            }
        }

        $this->log('A work in progress...');
    }
}

/*
Ref:
0:      1:      2:      3:      4:
 aaaa    ....    aaaa    aaaa    ....
b    c  .    c  .    c  .    c  b    c
b    c  .    c  .    c  .    c  b    c
 ....    ....    dddd    dddd    dddd
e    f  .    f  e    .  .    f  .    f
e    f  .    f  e    .  .    f  .    f
 gggg    ....    gggg    gggg    ....

  5:      6:      7:      8:      9:
 aaaa    aaaa    aaaa    aaaa    aaaa
b    .  b    .  .    c  b    c  b    c
b    .  b    .  .    c  b    c  b    c
 dddd    dddd    ....    dddd    dddd
.    f  e    f  .    f  e    f  .    f
.    f  e    f  .    f  e    f  .    f
 gggg    gggg    ....    gggg    gggg

Segments:
0 = 6
1 = 2
2 = 5
3 = 5
4 = 4
5 = 5
6 = 6
7 = 3
8 = 7
9 = 6
*/