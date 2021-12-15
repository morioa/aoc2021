<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day14
 * Extended Polymerization
 * https://adventofcode.com/2021/day/14
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day14 extends Common
{
    const PART_1_TEST_RESULT = 1588;
    const PART_2_TEST_RESULT = 2188189693529;
    const PART_1_STEPS = 10;
    const PART_2_STEPS = 40;

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
            $this->growPolymer();
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
        list($template, $pairs) = $data;
        $this->data['template'] = $template;
        $this->data['pairs'] = [];
        $pairs = explode("\n", $pairs);
        foreach ($pairs as $pair) {
            list($k, $v) = explode(' -> ', $pair);
            $this->data['pairs'][$k] = $v;
        }
        $this->log(['data' => $this->data]);

        $this->setPart($part);
    }

    public function growPolymer()
    {
        $steps = constant(__CLASS__ . "::PART_{$this->part}_STEPS");
        for ($i = 0; $i < $steps; $i++) {
            $newTemplate = '';
            $templateLen = strlen($this->data['template']);
            for ($j = 0; $j < ($templateLen - 1); $j++) {
                $checkStr = substr($this->data['template'], $j, 2);
                $appendStr = (isset($this->data['pairs'][$checkStr]))
                    ? substr_replace($checkStr, $this->data['pairs'][$checkStr], 1, 1)
                    : substr($checkStr, 0, 1);
                //$this->log(['checkStr' => $checkStr, 'appendStr' => $appendStr]);
                $newTemplate .= $appendStr;
            }
            $newTemplate .= substr($this->data['template'], -1);
            //$this->log(['old template' => $this->data['template'], 'old template length' => $templateLen, 'new template' => $newTemplate]);
            $this->data['template'] = $newTemplate;
        }

        $chars = [];
        for ($i = 0; $i < strlen($this->data['template']); $i++) {
            $char = substr($this->data['template'], $i, 1);
            if (!isset($chars[$char])) {
                $chars[$char] = 1;
            } else {
                $chars[$char] += 1;
            }
        }
        arsort($chars, SORT_NUMERIC);
        $mostCommon = array_shift($chars);
        $leastCommon = array_pop($chars);

        $calc = $mostCommon - $leastCommon; // count most common - count least common
        $this->log("Calculation result: {$calc}");

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $calc);
        }
    }
}