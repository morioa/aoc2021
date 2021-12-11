<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day9
 * Seven Smoke Basin
 * https://adventofcode.com/2021/day/9
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day9 extends Common
{
    const PART_1_TEST_RESULT = 15;
    const PART_2_TEST_RESULT = 1134;

    protected array $heightMap = [];

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
            $func = ($this->part === 1)
                ? 'calcRiskLevel'
                : 'findBasins';
            $this->$func();
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
        foreach ($data as $map) {
            $this->heightMap[] = str_split($map);
        }
        //$this->log(['height map' => $this->heightMap]);

        $this->setPart($part);
    }

    /**
     * Calculate the risk level by finding the low points
     * @return void
     * @throws Exception
     */
    public function calcRiskLevel()
    {
        $lowPoints = [];
        $rowsCount = count($this->heightMap);
        $colsCount = count($this->heightMap[0]);

        for ($i = 0; $i < $rowsCount; $i++) {
            for ($j = 0; $j < $colsCount; $j++) {
                $pointHeight = $this->heightMap[$i][$j];
                $u = $i - 1;
                $d = $i + 1;
                $l = $j - 1;
                $r = $j + 1;
                if (
                    (!isset($this->heightMap[$u][$j]) || $pointHeight < $this->heightMap[$u][$j])
                    && (!isset($this->heightMap[$d][$j]) || $pointHeight < $this->heightMap[$d][$j])
                    && (!isset($this->heightMap[$i][$l]) || $pointHeight < $this->heightMap[$i][$l])
                    && (!isset($this->heightMap[$i][$r]) || $pointHeight < $this->heightMap[$i][$r])
                ) {
                    $this->log(['low point' => "{$i},{$j}"]);
                    $lowPoints[] = $this->heightMap[$i][$j];
                }
            }
        }

        $riskLevel = array_sum($lowPoints) + count($lowPoints);
        //$this->log(['low points' => $lowPoints, 'risk level' => $riskLevel]);
        $this->log("Risk level: {$riskLevel}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $riskLevel);
        }
    }

    /**
     * Find the basins and add the sizes of the 3 largest
     * @return void
     * @throws Exception
     */
    public function findBasins()
    {
        $rowsCount = count($this->heightMap);
        $colsCount = count($this->heightMap[0]);

        $basinPoints = [];
        for ($i = 0; $i < $rowsCount; $i++) {
            for ($j = 0; $j < $colsCount; $j++) {
                $pointHeight = $this->heightMap[$i][$j];
                if ($pointHeight < 9) {
                    $basinPoints[] = "{$i},{$j}";
                }
            }
        }
        //$this->log(['basin points' => $basinPoints]);

        $basins = [];
        foreach ($basinPoints as $point) {
            $basinsCount = count($basins);

            list($x, $y) = explode(',', $point);
            $x1 = $x - 1;
            $x2 = $x + 1;
            $y1 = $y - 1;
            $y2 = $y + 1;
            $checkPoints = [
                "{$x},{$y}",
                "{$x},{$y1}",
                "{$x},{$y2}",
                "{$x1},{$y}",
                "{$x2},{$y}",
            ];

            $newBasin = [];
            foreach ($checkPoints as $checkPoint) {
                if (in_array($checkPoint, $basinPoints)) {
                    $newBasin[] = $checkPoint;
                }
            }

            $basinKey = null;
            for ($i = 0; $i < $basinsCount; $i++) {
                if (count(array_intersect($newBasin, $basins[$i]))) {
                    $basinKey = $i;
                    break;
                }
            }

            if (is_null($basinKey)) {
                $basins[$basinsCount] = $newBasin;
            } else {
                $basins[$basinKey] = array_unique(array_merge($basins[$basinKey], $newBasin));
            }
        }

        // merge basins
        $intersectExists = true;
        while ($intersectExists) {
            $basinsCount = count($basins);
            for ($i = 0; $i < $basinsCount; $i++) {
                for ($j = ($i + 1); $j < $basinsCount; $j++) {
                    if (!empty(array_intersect($basins[$i], $basins[$j]))) {
                        $basins[$i] = array_unique(array_merge($basins[$i], $basins[$j]));
                        unset($basins[$j]);
                        $basins = array_values($basins);
                        continue 3;
                    }
                }
            }
            $intersectExists = false;
        }
        //$this->log(['basins' => $basins]);

        $basinsBySize = [];
        foreach ($basins as $key => $basin) {
            $basinsBySize[$key] = count($basin);
        }
        rsort($basinsBySize);
        //$this->log(['basins by size' => $basinsBySize]);

        $basinsSize = $basinsBySize[0] * $basinsBySize[1] * $basinsBySize[2];
        $this->log("Basins size: {$basinsSize}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $basinsSize);
        }
    }
}