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
    public function run($dataFile)
    {
        try {
            $this->log('Started ' . __CLASS__);

            $this->init($this->loadData($dataFile));
            $part = $this->partInputRequest();
            $this->align($part === 2);
            $this->alignBruteForce($part === 2);
        } catch (Exception $e) {
            $this->log($e);
            exit(1);
        }
    }

    /**
     * Initialize data into class member variables
     * @param $data
     */
    public function init($data)
    {
        $this->crabs = explode(',', $data[0]);
        sort($this->crabs);
        //$this->log(['Crabs' => $this->crabs]);
    }

    /**
     * Use formulas to calculate fuel consumption
     *   - median (if constant fuel cost)
     *   - mean   (if increased fuel cost per step of shift)
     * @param false $fuelCostsIncrease
     */
    public function align($fuelCostsIncrease = false)
    {
        $count = count($this->crabs);
        $optimumFunc = ($fuelCostsIncrease) ? 'mean' : 'median';
        $optimumPos = $this->$optimumFunc();
        $this->log(['count' => $count, 'optimum func' => $optimumFunc, 'optimum pos' => $optimumPos]);
        $this->log(['mean' => $this->mean(), 'median' => $this->median()]);

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
                //$this->log("Fuel for pos: {$fuelForPos}");

                $fuel += $fuelForPos;
            }
        }
        $this->log("Optimal position (" . __FUNCTION__ . "): {$optimumPos}");
        $this->log("Fuel consumption (" . __FUNCTION__ . "): {$fuel}");
    }

    /**
     * Use brute force to calculate fuel consumption
     * @param false $fuelCostsIncrease
     */
    public function alignBruteForce($fuelCostsIncrease = false)
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
    }

    /**
     * Find the mean (average) of the crab positions
     * @return false|float
     */
    public function mean()
    {
        $count = count($this->crabs);
        return floor(array_sum($this->crabs) / $count);
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
