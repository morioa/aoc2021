<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day3
 * Binary Diagnostic
 * https://adventofcode.com/2021/day/3
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day3 extends Common
{
    const PART_1_TEST_RESULT = 198;
    const PART_2_TEST_RESULT = 230;

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
                ? 'calcPowerConsumption'
                : 'calcLifeSupportRating';
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
     * Calculate the power consumption by finding gamma and epsilon values
     * @param $data
     * @return void
     * @throws Exception
     */
    public function calcPowerConsumption($data)
    {
        $g = '';
        $e = '';

        $dataLength = strlen(trim($data[0]));
        $dataCount = count($data);
        for ($i = 0; $i < $dataLength; $i++) {
            $bitCounts = [0, 0];
            for ($j = 0; $j < $dataCount; $j++) {
                $bitValue = (int)substr($data[$j], $i, 1);
                $bitCounts[$bitValue]++;
            }
            //$this->log("Pos {$i} : 0={$bitCounts[0]} : 1={$bitCounts[1]}");

            $g .= ($bitCounts[0] > $bitCounts[1]) ? 0 : 1;
            $e .= ($bitCounts[0] > $bitCounts[1]) ? 1 : 0;
        }

        $gDecimal = bindec($g);
        $eDecimal = bindec($e);
        $p = $gDecimal * $eDecimal;

        $this->log("Data length: {$dataLength}");
        $this->log("Data count: {$dataCount}");
        $this->log("Gamma: {$gDecimal} ({$g})");
        $this->log("Epsilon: {$eDecimal} ({$e})");
        $this->log("Power consumption: {$p}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $p);
        }
    }

    /**
     * Calculate live support rating by determining O2
     * @param $data
     * @return void
     * @throws Exception
     */
    public function calcLifeSupportRating($data)
    {
        $o2Bits = $this->calcRating($data, 'o2');
        $co2Bits = $this->calcRating($data, 'co2');

        $o2Decimal = bindec($o2Bits);
        $co2Decimal = bindec($co2Bits);

        $lifeSupportRating = $o2Decimal * $co2Decimal;

        $this->log("O2 Rating: {$o2Decimal} ({$o2Bits})");
        $this->log("CO2 Rating: {$co2Decimal} ({$co2Bits})");
        $this->log("Life Support Rating: {$lifeSupportRating}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $lifeSupportRating);
        }
    }

    /**
     * Calculate rating
     * @param $data
     * @param $type
     * @return false|mixed|string
     * @throws Exception
     */
    public function calcRating($data, $type)
    {
        $bits = '';

        $dataLength = strlen(trim($data[0]));
        $dataCount = count($data);
        for ($i = 0; $i < $dataLength; $i++) {
            $bitCounts = [0, 0];
            for ($j = 0; $j < $dataCount; $j++) {
                $bitValue = (int)substr($data[$j], $i, 1);
                $bitCounts[$bitValue]++;
            }

            switch ($type) {
                case 'o2':
                    $bits .= ($bitCounts[1] >= $bitCounts[0]) ? 1 : 0;
                    break;

                case 'co2':
                    $bits .= ($bitCounts[0] <= $bitCounts[1]) ? 0 : 1;
                    break;

                default:
                    throw new Exception("Unhandled type: {$type}");
            }

            //$this->log("Matching bits: {$bits}");
            $bitMatchLength = strlen($bits);
            for ($k = 0; $k < $dataCount; $k++) {
                if (substr($data[$k], 0, $bitMatchLength) !== $bits) {
                    unset($data[$k]);
                }
            }

            $data = array_values($data); // reindex
            $dataCount = count($data);

            if ($dataCount === 1) {
                $bits = trim($data[0]);
                break;
            }
        }

        return $bits;
    }
}
