<?php

/**
 * Class Day3
 * Binary Diagnostic
 * https://adventofcode.com/2021/day/3
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day3 extends Common
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
                $this->calcPowerConsumption($data);
            } else {
                $this->calcLifeSupportRating($data);
            }
        } catch (Exception $e) {
            self::log($e);
            exit(1);
        }
    }

    /**
     * Calculate the power consumption by finding gamma and epsilon values
     * @param $data
     */
    function calcPowerConsumption($data)
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
            //self::log("Pos {$i} : 0={$bitCounts[0]} : 1={$bitCounts[1]}");

            $g .= ($bitCounts[0] > $bitCounts[1]) ? 0 : 1;
            $e .= ($bitCounts[0] > $bitCounts[1]) ? 1 : 0;
        }

        $gDecimal = bindec($g);
        $eDecimal = bindec($e);
        $p = $gDecimal * $eDecimal;

        self::log("Data length: {$dataLength}");
        self::log("Data count: {$dataCount}");
        self::log("Gamma: {$gDecimal} ({$g})");
        self::log("Epsilon: {$eDecimal} ({$e})");
        self::log("Power consumption: {$p}");
    }

    /**
     * Calculate live support rating by determining O2
     * @param $data
     * @throws Exception
     */
    function calcLifeSupportRating($data)
    {
        $o2Bits = $this->calcRating($data, 'o2');
        $co2Bits = $this->calcRating($data, 'co2');

        $o2Decimal = bindec($o2Bits);
        $co2Decimal = bindec($co2Bits);

        $lifeSupportRating = $o2Decimal * $co2Decimal;

        self::log("O2 Rating: {$o2Decimal} ({$o2Bits})");
        self::log("CO2 Rating: {$co2Decimal} ({$co2Bits})");
        self::log("Life Support Rating: {$lifeSupportRating}");
    }

    function calcRating($data, $type)
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

            //self::log("Matching bits: {$bits}");
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
