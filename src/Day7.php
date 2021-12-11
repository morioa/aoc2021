<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day7
 * The Treachery of Whales
 * https://adventofcode.com/2021/day/7
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day7 extends Common
{
    const PART_1_TEST_RESULT = 37;
    const PART_2_TEST_RESULT = 168;

    protected array $crabs = [];
    protected array $fuelCosts = [];

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
            $this->align($this->part === 2);
            $this->alignBruteForce($this->part === 2);
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
        $this->crabs = explode(',', $data[0]);
        sort($this->crabs);
        //$this->log(['Crabs' => $this->crabs]);

        $this->setPart($part);
    }

    /**
     * Use formulas to calculate fuel consumption
     *   - median (if constant fuel cost)
     *   - mean   (if increased fuel cost per step of shift)
     * @param bool $fuelCostsIncrease
     * @return void
     * @throws Exception
     */
    public function align(bool $fuelCostsIncrease = false)
    {
        $count = count($this->crabs);
        $func = ($fuelCostsIncrease) ? 'mean' : 'median';
        $alignPositions = [$this->$func()];
        if ($func === 'mean') {
            $alignPositions[] = $this->$func(true);
        }

        $this->log(['count' => $count, 'func' => $func, 'align positions' => $alignPositions]);

        $optimalFuel = 0;
        $optimalPos = null;
        foreach ($alignPositions as $alignPos) {
            $fuel = 0;
            foreach ($this->crabs as $pos) {
                $shift = abs($alignPos - $pos);
                if (!$fuelCostsIncrease) {
                    $fuel += $shift;
                } else {
                    $fuelForPos = 0;
                    for ($j = 1; $j <= $shift; $j++) {
                        $fuelForPos += $j;
                    }
                    //$this->log("Fuel for pos: {$fuelForPos}");

                    $fuel += $fuelForPos;
                }
            }

            if ($optimalFuel === 0 || $optimalFuel > $fuel) {
                $optimalFuel = $fuel;
                $optimalPos = $alignPos;
            }
        }

        $this->log("Optimal position (" . __FUNCTION__ . "): {$optimalPos}");
        $this->log("Fuel consumption (" . __FUNCTION__ . "): {$optimalFuel}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $fuel);
        }
    }

    /**
     * Use brute force to calculate fuel consumption
     * @param bool $fuelCostsIncrease
     * @return void
     * @throws Exception
     */
    public function alignBruteForce(bool $fuelCostsIncrease = false)
    {
        $uniquePositions = array_unique($this->crabs);
        $uniquePositionsCount = count($uniquePositions);

        // loop through unique positions
        for ($i = 0; $i < $uniquePositionsCount; $i++) {
            $fuel = 0;
            // loop through crabs
            foreach ($this->crabs as $pos) {
                $shift = abs($i - $pos);
                if (!$fuelCostsIncrease) {
                    $fuel += $shift;
                } else {
                    $fuelForPos = 0;
                    for ($k = 1; $k <= $shift; $k++) {
                        $fuelForPos += $k;
                    }
                    $fuel += $fuelForPos;
                }
            }
            $this->fuelCosts[$i] = $fuel;
        }
        asort($this->fuelCosts);
        $firstKey = array_key_first($this->fuelCosts);
        list($pos, $cost) = [$firstKey, $this->fuelCosts[$firstKey]];

        $this->log("Optimal position (" . __FUNCTION__ . "): {$pos}");
        $this->log("Fuel consumption (" . __FUNCTION__ . "): {$cost}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $cost);
        }
    }

    /**
     * Find the mean (average) of the crab positions
     * @param bool $roundUp
     * @return mixed
     */
    public function mean(bool $roundUp = false)
    {
        $func = ($roundUp) ? 'ceil' : 'floor';
        $count = count($this->crabs);
        return $func(array_sum($this->crabs) / $count);
    }

    /**
     * Find the median (middle value) of the crab positions
     * @return mixed
     */
    public function median()
    {
        $count = count($this->crabs);
        $key = floor($count / 2);
        return $this->crabs[$key];
    }
}
