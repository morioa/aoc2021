<?php
namespace AoC2021;

use Exception;
use ReflectionClass;

/**
 * Class Day10
 * Syntax Scoring
 * https://adventofcode.com/2021/day/10
 * @author Andrew Morio <morioa@hotmail.com>
 */
class Day10 extends Common
{
    const PART_1_TEST_RESULT = 26397;
    const PART_2_TEST_RESULT = 288957;

    protected array $data;
    protected array $bounds;

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

            $this->log("Executing part {$this->part}");
            $this->parseChunks();
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
        $this->data = $data;
        $this->bounds = [
            [
                'open' => '(',
                'close' => ')',
                'errorScore' => 3,
                'completionScore' => 1,
            ],
            [
                'open' => '[',
                'close' => ']',
                'errorScore' => 57,
                'completionScore' => 2,
            ],
            [
                'open' => '{',
                'close' => '}',
                'errorScore' => 1197,
                'completionScore' => 3,
            ],
            [
                'open' => '<',
                'close' => '>',
                'errorScore' => 25137,
                'completionScore' => 4,
            ],
        ];
        //$this->log(['data' => $this->data, 'bounds' => $this->bounds]);

        $this->setPart($part);
    }

    /**
     * Parse the chunks and calculate the scores
     * @return void
     * @throws Exception
     */
    public function parseChunks()
    {
        $syntaxErrors = [
            'chunks' => [],
            'chars' => [],
            'score' => 0,
        ];
        $incomplete = [
            'chunks' => [],
            'read' => [],
            'scores' => [],
        ];
        foreach ($this->data as $chunk) {
            $chunkRead = '';
            $chunkLength = strlen($chunk);
            for ($i = 0; $i < $chunkLength; $i++) {
                $currChar = substr($chunk, $i, 1);
                foreach (['open','close'] as $type) {
                    $closeChar = ($type === 'close');
                    $currCharBoundingKey = array_search($currChar, array_column($this->bounds, $type));
                    if ($currCharBoundingKey !== false) {
                        break;
                    }
                }

                $allowedChars = array_column($this->bounds, 'open');
                if ($chunkRead !== '') {
                    $prevChar = substr($chunkRead, -1, 1);
                    foreach (['open','close'] as $type) {
                        $prevCharBoundingKey = array_search($prevChar, array_column($this->bounds, $type));
                        if ($prevCharBoundingKey !== false) {
                            break;
                        }
                    }
                    $allowedChars[] = $this->bounds[$prevCharBoundingKey]['close'];
                }
                //$this->log(['chunk' => $chunk, 'chunk read' => $chunkRead, 'bounding key' => $currCharBoundingKey, 'allowed chars' => $allowedChars]);

                if (!in_array($currChar, $allowedChars)) {
                    $syntaxErrors['chunks'][] = $chunk;
                    $syntaxErrors['chars'][] = $currChar;
                    $syntaxErrors['score'] += $this->bounds[$currCharBoundingKey]['errorScore'];
                    continue 2;
                }

                if (in_array($currChar, array_column($this->bounds, 'close'))) {
                    $chunkRead = substr($chunkRead, 0, -1);
                } else {
                    $chunkRead .= $currChar;
                }
            }

            $incomplete['chunks'][] = $chunk;
            $incomplete['read'][] = $chunkRead;
        }
        //$this->log(['syntax errors' => $syntaxErrors, 'incomplete' => $incomplete]);

        if ($this->part === 1) {
            $score = $syntaxErrors['score'];
            $this->log("Syntax error score: {$score}");
        } else {
            $incompleteCount = count($incomplete['read']);
            for ($i = 0; $i < $incompleteCount; $i++) {
                $incomplete['scores'][$i] = 0;
                $chars = str_split(strrev($incomplete['read'][$i]));
                foreach ($chars as $char) {
                    $charBoundingKey = array_search($char, array_column($this->bounds, 'open'));
                    $incomplete['scores'][$i] = ($incomplete['scores'][$i] * 5) + $this->bounds[$charBoundingKey]['completionScore'];
                }
            }
            //$this->log(['incomplete' => $incomplete]);

            $score = $this->median($incomplete['scores']);
            $this->log("Completion score: {$score}");
        }

        if ($this->isTest) {
            $this->compareResults(__CLASS__, $this->part, $score);
        }
    }

    /**
     * Find the median (middle value) in the array
     * @param $arr
     * @return mixed
     */
    public function median($arr)
    {
        sort($arr, SORT_NUMERIC);
        $count = count($arr);
        $key = floor($count / 2);
        return $arr[$key];
    }
}