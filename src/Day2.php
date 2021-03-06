<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day2
 * Dive!
 * https://adventofcode.com/2021/day/2
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day2 extends Common
{
    const PART_1_TEST_RESULT = 150;
    const PART_2_TEST_RESULT = 900;

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
                ? 'findPosition'
                : 'findPositionUsingAim';
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
     * Find submarine position using up/down for depth
     * @param $data
     * @return void
     * @throws Exception
     */
    public function findPosition($data)
    {
        $x = 0;
        $y = 0;

        foreach ($data as $move) {
            list($direction, $units) = explode(' ', $move);

            switch ($direction) {
                case 'forward':
                    $x += (int)$units;
                    break;

                case 'down':
                    $y += (int)$units;
                    break;

                case 'up':
                    $y -= (int)$units;
                    break;

                default:
                    throw new Exception("Unhandled direction: {$direction}");
            }
        }

        $pos = $x * $y;

        $this->log("Horizontal: {$x}");
        $this->log("Depth: {$y}");
        $this->log("Calculated Position: {$pos}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $pos);
        }
    }

    /**
     * Find submarine position using up/down for aim instead of depth
     * @param $data
     * @return void
     * @throws Exception
     */
    public function findPositionUsingAim($data)
    {
        $x = 0; // horizontal
        $y = 0; // depth
        $a = 0; // aim

        foreach ($data as $move) {
            list($direction, $units) = explode(' ', $move);

            switch ($direction) {
                case 'forward':
                    $x += (int)$units;
                    $y += (int)$units * $a;
                    break;

                case 'down':
                    $a += (int)$units;
                    break;

                case 'up':
                    $a -= (int)$units;
                    break;

                default:
                    throw new Exception("Unhandled direction: {$direction}");
            }
        }

        $pos = $x * $y;

        $this->log("Horizontal: {$x}");
        $this->log("Depth: {$y}");
        $this->log("Calculated Position: {$pos}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $pos);
        }
    }
}
