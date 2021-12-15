<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day13
 * Transparent Origami
 * https://adventofcode.com/2021/day/13
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day13 extends Common
{
    const PART_1_TEST_RESULT = 17;
    const PART_2_TEST_RESULT = 16;

    protected array $data;

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
            $this->init($part, $this->loadData($dataFile, "\n\n"));

            $this->log("Executing part {$this->part}");
            $this->doFolding($this->part === 1);
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
        list($points, $folds) = $data;
        $this->data['points'] = explode("\n", $points);
        $this->data['folds'] = array_map(
            function($v) {
                $v = explode(' ', $v);
                return array_pop($v);
            },
            explode("\n", $folds)
        );
        //$this->log(['data' => $this->data]);

        $this->setPart($part);
    }

    public function doFolding($stopOnFirstFold = false)
    {
        //$this->plotPoints();

        foreach ($this->data['folds'] as $fold) {
            list($xy, $pos) = explode('=', $fold);
            $this->data["{$xy}Max"] = $pos;

            for ($i = 0; $i < count($this->data['points']); $i++) {
                list($x, $y) = explode(',', $this->data['points'][$i]);
                $xyCalc = $$xy;
                if ($xyCalc < $pos) {            // ignore
                    //$this->log(['fold' => $fold, 'old point' => "{$x},{$y}", 'new point' => 'ignored']);
                    continue;
                } elseif ($xyCalc === $pos) {    // remove
                    $this->data['points'][$i] = null;
                    //$this->log(['fold' => $fold, 'old point' => "{$x},{$y}", 'new point' => 'removed']);
                    continue;
                } elseif ($xyCalc > $pos) {      // fold
                    $xyCalc -= ($xyCalc - $pos) * 2;
                    $this->data['points'][$i] = ($xy === 'x')
                        ? "{$xyCalc},{$y}"
                        : "{$x},{$xyCalc}";
                    //$this->log(['fold' => $fold, 'old point' => "{$x},{$y}", 'new point' => $this->data['points'][$i]]);
                }
            }
            $this->data['points'] = array_values(array_unique($this->data['points']));
            //$this->log(['new data points' => $this->data['points']]);

            if ($stopOnFirstFold) {
                break;
            }
        }

        //$this->log(['points' => $this->data['points']]);
        $pointsCount = count($this->data['points']);
        $this->log("Points after folding: {$pointsCount}");

        if ($this->isTest || $this->part !== 1) {
            $this->plotPoints();
        }

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $pointsCount);
        }
    }

    public function plotPoints()
    {
        $xMax = $this->data['xMax'] ?? 0;
        $yMax = $this->data['yMax'] ?? 0;

        $lines = [];
        foreach ($this->data['points'] as $point) {
            list($x, $y) = explode(',', $point);
            $xMax = ($x > $xMax) ? $x : $xMax;
            $yMax = ($y > $yMax) ? $y : $yMax;
            if (!isset($lines[$y])) {
                $lines[$y] = [];
            }
            $lines[$y][$x] = '#';
        }
        ksort($lines);
        array_walk($lines, 'ksort');
        //$this->log(['points' => $this->data['points'], 'lines' => $lines, 'xMax' => $xMax, 'yMax' => $yMax]);

        $plotLines = [];
        for ($y = 0; $y < $yMax; $y++) {
            $plotLine = '';
            for ($x = 0; $x < $xMax; $x++) {
                $plotLine .= (isset($lines[$y][$x]))
                    ? '█'
                    : '.';
            }
            $plotLines[] = $plotLine;
        }

        print "\n" . implode("\n", $plotLines) . "\n\n";
    }
}