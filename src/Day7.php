<?php

/**
 * Class Day7
 * The Treachery of Whales
 * https://adventofcode.com/2021/day/7
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day7 extends Common
{
    protected $crabs = [];
    protected $fuelCosts = [];

    /**
     * Run method executed at script start
     * @param $dataFile
     */
    function run($dataFile)
    {
        try {
            self::log('Started ' . __CLASS__);

            $this->init($this->loadData($dataFile));
            $part = $this->partInputRequest();
            $this->align($part === 2);
            $this->alignBruteForce($part === 2);
        } catch (Exception $e) {
            self::log($e);
            exit(1);
        }
    }

    /**
     * Initialize data into class member variables
     * @param $data
     */
    function init($data)
    {
        $this->crabs = explode(',', $data[0]);
        sort($this->crabs);
        //self::log(['Crabs' => $this->crabs]);
    }

    /**
     * Use formulas to calculate fuel consumption
     *   - median (if constant fuel cost)
     *   - mean   (if increased fuel cost per step of shift)
     * @param false $fuelCostsIncrease
     */
    function align($fuelCostsIncrease = false)
    {
        $count = count($this->crabs);
        $optimumFunc = ($fuelCostsIncrease) ? 'mean' : 'median';
        $optimumPos = $this->$optimumFunc();
        self::log(['count' => $count, 'optimum func' => $optimumFunc, 'optimum pos' => $optimumPos]);
        self::log(['mean' => $this->mean(), 'median' => $this->median()]);

        $fuel = 0;
        foreach ($this->crabs as $pos) {
            $shift = abs($optimumPos - $pos);
            if (!$fuelCostsIncrease) {
                $fuel += $shift;
            } else {
                $fuelForPos = 0;
                for ($j = 1; $j <= $shift; $j++) {
                    $fuelForPos += $j;
                }
                //self::log("Fuel for pos: {$fuelForPos}");

                $fuel += $fuelForPos;
            }
        }
        self::log("Optimal position (" . __FUNCTION__ . "): {$optimumPos}");
        self::log("Fuel consumption (" . __FUNCTION__ . "): {$fuel}");
    }

    /**
     * Use brute force to calculate fuel consumption
     * @param false $fuelCostsIncrease
     */
    function alignBruteForce($fuelCostsIncrease = false)
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

        self::log("Optimal position (" . __FUNCTION__ . "): {$pos}");
        self::log("Fuel consumption (" . __FUNCTION__ . "): {$cost}");
    }

    /**
     * Find the mean (average) of the crab positions
     * @return false|float
     */
    function mean()
    {
        $count = count($this->crabs);
        return floor(array_sum($this->crabs) / $count);
    }

    /**
     * Find the median (middle value) of the crab positions
     * @return mixed
     */
    function median()
    {
        $count = count($this->crabs);
        $key = floor($count / 2);
        return $this->crabs[$key];
    }
}
