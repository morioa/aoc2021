<?php

/**
 * Class Day2
 * Dive!
 * https://adventofcode.com/2021/day/2
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day2 extends Common
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

            if ($this->partInputRequest() === 1) {
                $this->findPosition($data);
            } else {
                $this->findPositionUsingAim($data);
            }
        } catch (Exception $e) {
            self::log($e);
            exit(1);
        }
    }

    /**
     * Find submarine position using up/down for depth
     * @param $data
     * @throws Exception
     */
    function findPosition($data)
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

        self::log("Horizontal: {$x}");
        self::log("Depth: {$y}");
        self::log("Calculated Position: {$pos}");
    }

    /**
     * Find submarine position using up/down for aim instead of depth
     * @param $data
     * @throws Exception
     */
    function findPositionUsingAim($data)
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

        self::log("Horizontal: {$x}");
        self::log("Depth: {$y}");
        self::log("Calculated Position: {$pos}");
    }
}
