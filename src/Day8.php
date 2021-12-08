<?php

/**
 * Class Day8
 * Seven Segment Search
 * https://adventofcode.com/2021/day/8
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day8 extends Common
{
    protected array $patterns = [];

    /**
     * Run method executed at script start
     * @param $dataFile
     */
    public function run($dataFile)
    {
        try {
            $this->log('Started ' . __CLASS__);

            $this->init($this->loadData($dataFile));
            $this->signalDecode();
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
        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
            list($l, $r) = explode(' | ', $data[$i]);
            $this->patterns[$i]['source'] = explode(' ', $l);
            $this->patterns[$i]['output'] = explode(' ', $r);
        }
        $this->log(['patterns' => $this->patterns]);
    }

    public function signalDecode()
    {
        $displays = [
            0 => 'abcefg',
            1 => 'cf',
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',
            8 => 'abcdefg',
            9 => 'abcdfg',
        ];

        $this->log('This is a work in progress...');
    }
}

/*
Ref:
0:      1:      2:      3:      4:
 aaaa    ....    aaaa    aaaa    ....
b    c  .    c  .    c  .    c  b    c
b    c  .    c  .    c  .    c  b    c
 ....    ....    dddd    dddd    dddd
e    f  .    f  e    .  .    f  .    f
e    f  .    f  e    .  .    f  .    f
 gggg    ....    gggg    gggg    ....

  5:      6:      7:      8:      9:
 aaaa    aaaa    aaaa    aaaa    aaaa
b    .  b    .  .    c  b    c  b    c
b    .  b    .  .    c  b    c  b    c
 dddd    dddd    ....    dddd    dddd
.    f  e    f  .    f  e    f  .    f
.    f  e    f  .    f  e    f  .    f
 gggg    gggg    ....    gggg    gggg

Segments:
0 = 6
1 = 2
2 = 5
3 = 5
4 = 4
5 = 5
6 = 6
7 = 3
8 = 7
9 = 6
*/