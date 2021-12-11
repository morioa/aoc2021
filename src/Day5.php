<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day5
 * Hydrothermal Venture
 * https://adventofcode.com/2021/day/5
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day5 extends Common
{
    const PART_1_TEST_RESULT = 5;
    const PART_2_TEST_RESULT = 12;

    protected array $lineCoords = [];
    protected array $pointCoords = [];

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

            $this->init($part, $data);
            $this->convertLinesToPoints();
            $this->countIntersectPoints();
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
        $this->setPart($part);

        $hvLinesOnly = ($this->part === 1);

        foreach ($data as $line) {
            list($a, $b) = explode(' -> ', $line);
            list($ax, $ay) = explode(',', $a);
            list($bx, $by) = explode(',', $b);

            // if limiting to horizontal and vertical lines only
            if ($hvLinesOnly && ($ax !== $bx && $ay !== $by)) {
                continue;
            }

            $this->lineCoords[] = [
                'x1' => (int)$ax,
                'y1' => (int)$ay,
                'x2' => (int)$bx,
                'y2' => (int)$by,
            ];
        }

        //$this->log(['Line Coordinates' => $this->lineCoords]);
    }

    /**
     * Loop through line coordinates and convert lines to point coordinates
     * and keep track of how many times a point has been encountered
     * @return void
     * @throws Exception
     */
    public function convertLinesToPoints()
    {
        $lineCoordIndex = 0;
        foreach ($this->lineCoords as $line) {
            list($x1, $y1, $x2, $y2) = array_values($line);

            if($x1 === $x2 && $y1 === $y2) {
                $this->log(['Zero length line' => $line]);
                $this->incrementPointCoordCounter($x1, $y1);
                $this->lineCoords[$lineCoordIndex]['points'][] = "{$x1},{$y1}";
                continue;
            }

            if ($x1 !== $x2) {
                $comp1 = $x1;
                $comp2 = $x2;
            } else {
                $comp1 = $y1;
                $comp2 = $y2;
            }

            $xHi = ($comp1 > $comp2) ? $x1 : $x2;
            $xLo = ($comp1 > $comp2) ? $x2 : $x1;
            $yHi = ($comp1 > $comp2) ? $y1 : $y2;
            $yLo = ($comp1 > $comp2) ? $y2 : $y1;

            $dx = $xHi - $xLo;
            $dy = $yHi - $yLo;

            if ($dx !== 0) {  // non-vertical line
                for ($x = $xLo; $x <= $xHi; $x++) {
                    //$this->log("y = {$y1} + {$dy} * ({$x} - {$x1}) / {$dx}");
                    $y = $y1 + $dy * ($x - $x1) / $dx;
                    $this->incrementPointCoordCounter($x, $y);
                    $this->lineCoords[$lineCoordIndex]['points'][] = "{$x},{$y}";
                }
            } else {          // vertical line
                for ($y = $yLo; $y <= $yHi; $y++) {
                    //$this->log("x = {$x1} + {$dx} * ({$y} - {$y1}) / {$dy}");
                    $x = $x1 + $dx * ($y - $y1) / $dy;
                    $this->incrementPointCoordCounter($x, $y);
                    $this->lineCoords[$lineCoordIndex]['points'][] = "{$x},{$y}";
                }
            }

            $lineCoordIndex++;
        }

        //$this->log(['Point Coordinates' => $this->pointCoords]);
    }

    /**
     * Increment the counter for the target point
     * @param $x
     * @param $y
     * @return void
     */
    public function incrementPointCoordCounter($x, $y)
    {
        $pointCoord = "{$x},{$y}";
        if (!isset($this->pointCoords[$pointCoord])) {
            $this->pointCoords[$pointCoord] = 1;
        } else {
            $this->pointCoords[$pointCoord]++;
        }
    }

    /**
     * Count the number of points that have been used by more than one line
     * @return void
     * @throws Exception
     */
    public function countIntersectPoints()
    {
        //$this->log(['Line Coordinates' => $this->lineCoords]);

        $intersects = array_filter($this->pointCoords, function($count) {
            return ($count > 1);
        });
        //$this->log(['Intersect points' => $intersects]);

        $intersectsCount = count($intersects);
        $this->log("Intersect points count: {$intersectsCount}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $intersectsCount);
        }
    }
}
