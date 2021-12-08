<?php

/**
 * Class Day4
 * Giant Squid (Bingo!)
 * https://adventofcode.com/2021/day/4
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day4 extends Common
{
    protected $numbers = [];
    protected $boards = [];
    protected $winners = [];

    /**
     * Run method executed at script start
     * @param $dataFile
     */
    function run($dataFile)
    {
        try {
            self::log('Started ' . __CLASS__);

            $this->init($this->loadData($dataFile, "\n\n"));
            $this->draw($this->partInputRequest() === 1);
            $this->calcFinalScore();
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
        $this->numbers = [
            'drawOrder' => explode(',', array_shift($data)),
            'drawn' => [],
        ];
        $this->winners = [
            'boards' => [],
            'lastWinner' => [],
            'lastWinningNumberDrawn' => null,
        ];

        $boardsCount = count($data);
        for ($i = 0; $i < $boardsCount; $i++) {
            $this->boards[$i] = [
                'numbers' => [],
                'combos' => [],
                'matched' => [],
            ];
            $boardRows = explode("\n", trim($data[$i]));
            $boardRowsCount = count($boardRows);
            $boardColumns = [];
            for ($j = 0; $j < $boardRowsCount; $j++) {
                $boardRows[$j] = explode(' ', trim(preg_replace('/\s+/', ' ', $boardRows[$j])));
                $this->boards[$i]['combos'][] = $boardRows[$j];

                $this->boards[$i]['numbers'] = array_merge($this->boards[$i]['numbers'], $boardRows[$j]);

                $boardColumnsCount = count($boardRows[$j]);
                for ($k = 0; $k < $boardColumnsCount; $k++) {
                    $boardColumns[$k][] = $boardRows[$j][$k];
                }
            }
            $this->boards[$i]['combos'] = array_merge($this->boards[$i]['combos'], $boardColumns);

            $boardCombosCount = count($this->boards[$i]['combos']);
            $this->boards[$i]['matched'] = array_pad([], $boardCombosCount, []);
        }

        //self::log(['Numbers' => $this->numbers]);
        //self::log(['Boards' => $this->boards]);
    }

    /**
     * Loop through numbers in draw order and determine winner
     * @param false $stopOnFirstWin
     */
    function draw($stopOnFirstWin = false)
    {
        $boardsCount = count($this->boards);

        foreach ($this->numbers['drawOrder'] as $drawnNumber) {
            $this->numbers['drawn'][] = $drawnNumber;
            for ($i = 0; $i < $boardsCount; $i++) {
                $boardCombosCount = count($this->boards[$i]['combos']);
                for ($j = 0; $j < $boardCombosCount; $j++) {
                    if (in_array($drawnNumber, $this->boards[$i]['combos'][$j])) {
                        $this->boards[$i]['matched'][$j][] = $drawnNumber;
                        if (count($this->boards[$i]['matched'][$j]) === count($this->boards[$i]['combos'][$j])
                            && !isset($this->winners['boards'][$i])) {
                            $this->winners['boards'][$i] = $j;
                            $this->winners['lastWinner'] = [
                                'board' => $i,
                                'combo' => $j,
                            ];
                            $this->winners['lastWinningNumberDrawn'] = $drawnNumber;

                            if ($stopOnFirstWin) {
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Calculate the final score for the winner
     */
    function calcFinalScore()
    {
        $winningNumber = $this->winners['lastWinningNumberDrawn'];
        $winningBoard = $this->winners['lastWinner']['board'];
        $winningCombo = $this->winners['lastWinner']['combo'];

        $winningNumberIndex = array_search($winningNumber, $this->numbers['drawn']);
        $numbersDrawnTilFinalWin = array_slice($this->numbers['drawn'], 0, ($winningNumberIndex + 1));

        //self::log(['Boards' => $this->boards]);
        self::log("Last drawn winning number: {$winningNumber}");
        self::log("Winning board: {$winningBoard}");
        self::log("Winning combo: " . implode(', ', $this->boards[$winningBoard]['combos'][$winningCombo]));
        self::log("Winning board numbers: " . implode(', ', $this->boards[$winningBoard]['numbers']));

        $unmarkedNumbers = array_diff($this->boards[$winningBoard]['numbers'], $numbersDrawnTilFinalWin);
        self::log("Winning board unmarked numbers: " . implode(', ', $unmarkedNumbers));

        $unmarkedSum = array_sum($unmarkedNumbers);
        $score = $unmarkedSum * $winningNumber;
        self::log("Score: {$score}");
    }
}
