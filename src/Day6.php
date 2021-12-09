<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day6
 * Lanternfish
 * https://adventofcode.com/2021/day/6
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day6 extends Common
{
    const PART_1_TEST_RESULT = 5934;
    const PART_2_TEST_RESULT = 26984457539;
    const PART_1_CYCLES = 80;
    const PART_2_CYCLES = 256;
    const GESTATION_PARENT = 6;
    const GESTATION_CHILD = 8;

    protected array $fish = [];
    protected int $initialFishCount = 0;

    /**
     * Run method executed at script start
     * @param $dataFile
     */
    public function run($dataFile)
    {
        try {
            $this->log('Started ' . (new ReflectionClass($this))->getShortName());

            $this->init($this->loadData($dataFile));
            $this->runCycles(constant(__CLASS__ . '::PART_' . $this->part . '_CYCLES'));
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
        $fish = explode(',', $data[0]);
        for ($i = 0; $i <= self::GESTATION_CHILD; $i++) {
            $this->fish[$i] = 0;
        }
        foreach ($fish as $counter) {
            $this->fish[$counter]++;
        }

        $this->initialFishCount = array_sum($this->fish);
        $this->log(['Initial fish' => $this->fish, 'Initial fish count' => $this->initialFishCount]);

        $this->getPartInput();
    }

    /**
     * Perform iteration through cycles and output number of fish when complete
     * @param $cycles
     */
    public function runCycles($cycles)
    {
        $this->log("Running {$cycles} cycles");

        $fishCounters = count($this->fish);
        for ($i = 0; $i < $cycles; $i++) {
            // shift counters over
            $cycleFishCounters = [];
            for ($j = ($fishCounters - 1); $j >= 0; $j--) {
                $cycleFishCounters[($j - 1)] = $this->fish[$j];
            }

            // add -1 count (parents) to counter matching parent gestation
            // copy -1 count (parents) to counter matching child gestation
            // otherwise set counter matching child gestation to 0 if no new parents
            if ($cycleFishCounters[-1] > 0) {
                $cycleFishCounters[self::GESTATION_PARENT] += $cycleFishCounters[-1];
                $cycleFishCounters[self::GESTATION_CHILD] = $cycleFishCounters[-1];
                unset($cycleFishCounters[-1]);
            } else {
                $cycleFishCounters[self::GESTATION_CHILD] = 0;
            }

            // save updated counters
            $this->fish = $cycleFishCounters;
        }

        ksort($this->fish);
        $fishCount = array_sum($this->fish);
        $this->log(['Final fish' => $this->fish, 'Final fish count' => $fishCount]);
        $this->log("Fish count: {$fishCount}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $fishCount);
        }
    }
}
