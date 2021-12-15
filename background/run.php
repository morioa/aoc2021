<?php
/**
 * Background script to execute AoC 2021 challenges
 * https://adventofcode.com/
 * @author Andrew Morio <morioa@hotmail.com>
 */

namespace AoC2021;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use Bart\EscapeColors;
use Exception;

/**
 * Class Run
 */
class Run
{
    protected string $srcPath;
    protected string $assetsPath;
    protected array $days;
    protected string $daysRange;
    protected array $parts = [1, 2];
    protected string $mode = 'command';
    protected bool $audible = false;

    protected int $day = 0;
    protected int $part = 0;

    protected string $srcBaseClass;
    protected string $srcClass;
    protected string $srcClassFile;

    protected string $dataFile;
    protected array $commands = [
        'help' => [
            'alias' => 'h',
            'desc' => 'Shows this help.',
        ],
        'days' => [
            'alias' => 'd',
            'desc' => 'Shows available DAY values for run and test commands.',
        ],
        'parts' => [
            'alias' => 'p',
            'desc' => 'Shows available PART values for run and test commands.',
        ],
        'run' => [
            'alias' => 'r',
            'desc' => 'Run process for the specified DAY and PART using real data.',
            'args' => '[DAY] [PART]',
        ],
        'test' => [
            'alias' => 't',
            'desc' => 'Run process for the specified DAY and PART using test data.',
            'args' => '[DAY] [PART]',
        ],
        'int' => [
            'alias' => 'i',
            'desc' => 'Switch to interactive mode.',
        ],
        'audible' => [
            'alias' => 'a',
            'desc' => 'Toggle on/off audible alerts for execution completion.',
        ],
        'quit' => [
            'alias' => 'q',
            'desc' => 'Terminate the application.',
        ],
    ];

    /**
     * Run constructor
     * @throws Exception
     */
    public function __construct()
    {
        $this->showTitle();
        $this->showHelp();
        $this->init();
    }

    /**
     * Initialize class member variables
     * @return void
     * @throws Exception
     */
    public function init()
    {
        // Set paths
        $this->srcPath = realpath(__DIR__ . '/../src/') . DIRECTORY_SEPARATOR;
        $this->assetsPath = realpath(__DIR__ . '/../assets/') . DIRECTORY_SEPARATOR;

        // Get a listing of valid days
        $this->days = array_map(function($filepath){
            preg_match('/Day(?<day>\d+)\.php$/', $filepath, $match);
            return $match['day'];
        }, glob($this->srcPath . 'Day*.php'));
        sort($this->days, SORT_NUMERIC);

        // Prepare day range output for user input
        $daysCount = count($this->days);
        if ($daysCount === 0) {
            throw new Exception('No days available to run');
        } elseif ($daysCount === 1) {
            $this->daysRange = $this->days[0];
        } else {
            $this->daysRange = "{$this->days[0]}-{$this->days[$daysCount - 1]}";
        }

        $this->showDays();
        $this->showParts();
        $this->showAudibleStatus();
    }

    /**
     * Begin the application
     */
    public function start()
    {
        try {
            if ($this->mode === 'command') {
                $this->getCommandInputs();
            } else {
                $this->setSrcVars();
                $this->setDataVars();
            }

            if ($this->mode !== 'command') {
                $this->run();
            }

            $this->restart();
        } catch (Exception $e) {
            die("\nException caught: {$e->getMessage()}\n");
        }
    }

    /**
     * Restart application
     * @return void
     * @throws Exception
     */
    public function restart()
    {
        $this->day = 0;
        $this->part = 0;

        if ($this->mode === 'command') {
            $this->start();
        } else {
            $this->getRestartInput();
        }
    }

    /**
     * Display title to screen
     * @return void
     * @throws Exception
     */
    public function showTitle()
    {
        print EscapeColors::fg_color(TITLE_FG_COLOR, PROJECT_TITLE) . "\n";
    }

    /**
     * Get user command inputs
     * @return void
     * @throws Exception
     */
    public function getCommandInputs()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\n> ");
        $inputs = preg_split('/\s+/', trim(fgets(STDIN)));

        $command = $inputs[0];
        $day = $inputs[1] ?? 0;
        $part = $inputs[2] ?? 0;
        //print_r(['command' => $command, 'day' => $day, 'part' => $part]);

        if (!in_array($command, array_merge(array_keys($this->commands), array_column($this->commands, 'alias')))) {
            $this->inputError("Invalid command entry: {$command}", 'showHelpAlert');
        } else {
            switch ($command) {
                case 'h':
                case 'help':
                    $this->showHelp();
                    break;

                case 'd':
                case 'days':
                    $this->showDays();
                    break;

                case 'p':
                case 'parts':
                    $this->showParts();
                    break;

                case 'r':
                case 'run':
                    $this->setSrcVars($day);
                    $this->setPartVar($part);
                    $this->setDataVars(false);
                    $this->run();
                    break;

                case 't':
                case 'test':
                    $this->setSrcVars($day);
                    $this->setPartVar($part);
                    $this->setDataVars(true);
                    $this->run();
                    break;

                case 'i':
                case 'int':
                    $this->mode = 'interactive';
                    $this->start();
                    break;

                case 'a':
                case 'audible':
                    $this->audible = (!$this->audible);
                    $this->showAudibleStatus();
                    break;

                case 'q':
                case 'quit':
                    exit(0);

                default:
                    $this->inputError("Invalid command entry: {$command}", 'showHelpAlert');
            }
        }
    }

    /**
     * Trigger run method for target source
     * @return void
     */
    public function run()
    {
        // Include day source file and run
        require_once $this->srcClassFile;
        $day = new $this->srcClass;
        $day->run($this->srcDataFile, $this->part);
        $this->beep();
    }

    /**
     * Display command help suggestion
     * @return void
     * @throws Exception
     */
    public function showHelpAlert()
    {
        print "Type " . EscapeColors::fg_color(COMMAND_FG_COLOR, 'h') . " or " . EscapeColors::fg_color(COMMAND_FG_COLOR, 'help') . " to show a list of available commands.\n";
    }

    /**
     * Show the help details
     * @return void
     * @throws Exception
     */
    public function showHelp()
    {
        $help = <<<HELP
This is a PHP approach to handling the Advent of Code 2021 challenge puzzles.

Commands:

%s
Example commands:

%s
HELP;
        $indent = str_repeat(' ', 4);

        $commandsHelpText = '';
        foreach ($this->commands as $command => $commandDetails) {
            $alias = $commandDetails['alias'] ?? '';
            $args = $commandDetails['args'] ?? '';
            $desc = $commandDetails['desc'];

            $commandUnformatted = $command;
            $aliasUnformatted = ($alias != '')
                ? "{$alias}, "
                : '';
            $argsUnformatted = ($args != '')
                ? " {$args}"
                : '';

            $commandFormatted = EscapeColors::fg_color(COMMAND_FG_COLOR, $command);
            $aliasFormatted = ($alias !== '')
                ? EscapeColors::fg_color(COMMAND_FG_COLOR, $alias) . ', '
                : '';
            $argsFormatted = ($args !== '')
                ? ' ' . EscapeColors::fg_color(COMMAND_ARG_FG_COLOR, $args)
                : '';

            $unformattedConcat = $aliasUnformatted . $commandUnformatted . $argsUnformatted;
            $spacer = str_repeat(' ', 24 - strlen($unformattedConcat));

            $formattedConcat = $aliasFormatted . $commandFormatted . $argsFormatted;
            $commandsHelpText .= $indent . $formattedConcat . $spacer . "{$desc}\n";
        }

        $exampleCommands = [
            [
                'desc' => "Run day 3 part 2 using test input data. Results are known for test executions, so\n{$indent}result comparisons are included.",
                'commands' => [
                    EscapeColors::fg_color(COMMAND_FG_COLOR, 't') . EscapeColors::fg_color(COMMAND_ARG_FG_COLOR, ' 3 2'),
                    EscapeColors::fg_color(COMMAND_FG_COLOR, 'test') . EscapeColors::fg_color(COMMAND_ARG_FG_COLOR, ' 3 2'),
                ],
            ],
            [
                'desc' => 'Run day 6 part 1 using real input data.',
                'commands' => [
                    EscapeColors::fg_color(COMMAND_FG_COLOR, 'r') . EscapeColors::fg_color(COMMAND_ARG_FG_COLOR, ' 6 1'),
                    EscapeColors::fg_color(COMMAND_FG_COLOR, 'run') . EscapeColors::fg_color(COMMAND_ARG_FG_COLOR, ' 6 1'),
                ],
            ],
        ];
        $exampleCommandsText = '';
        foreach ($exampleCommands as $example) {
            $exampleCommandsText .= $indent . $example['desc'] . "\n" . $indent . $indent . implode('    OR    ', $example['commands']) . "\n\n";
        }

        $help = sprintf($help, $commandsHelpText, $exampleCommandsText);

        print "{$help}";
    }

    /**
     * Display valid days
     * @return void
     * @throws Exception
     */
    public function showDays()
    {
        print 'Available days: ' . EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, $this->daysRange) . "\n";
    }

    /**
     * Display valid parts
     * @return void
     * @throws Exception
     */
    public function showParts()
    {
        print 'Available parts: ' . EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, implode('-', $this->parts)) . "\n";
    }

    /**
     * Display audible status
     * @return void
     * @throws Exception
     */
    public function showAudibleStatus()
    {
        $audibleVerbose = ($this->audible) ? 'on' : 'off';
        print 'Audible alerts: ' . EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, $audibleVerbose) . "\n";
    }

    /**
     * Set class member variables related to source
     * @param $day
     * @return void
     * @throws Exception
     */
    public function setSrcVars($day = null)
    {
        $day = $day ?? $this->getDayInput();
        if (!in_array($day, $this->days)) {
            $this->inputError("Invalid day entry: {$day}", 'showDays');
        } else {
            $this->day = $day;
        }

        // Check existence of day source file
        $this->srcBaseClass = "Day{$this->day}";
        $this->srcClass = "AoC2021\\{$this->srcBaseClass}";
        $this->srcClassFile = $this->srcPath . "{$this->srcBaseClass}.php";
        if (!file_exists($this->srcClassFile)) {
            $this->inputError("Missing source file: \"{$this->srcClassFile}\"");
        }
    }

    /**
     * Set class member variables related to data
     * @param $isTest
     * @return void
     * @throws Exception
     */
    public function setDataVars($isTest = null)
    {
        $isTest = $isTest ?? $this->getTestInput();
        $testStr = ($isTest) ? 'test' : '';

        // Check existence of day data file
        $this->srcDataFile = $this->assetsPath . "{$this->srcBaseClass}_{$testStr}input.txt";
        if (!file_exists($this->srcDataFile)) {
            $this->inputError("Missing data file: \"{$this->srcDataFile}\"");
        }
    }

    /**
     * Set class member variable for part
     * @param $part
     * @return void
     * @throws Exception
     */
    public function setPartVar($part)
    {
        if (!in_array($part, $this->parts)) {
            $this->inputError("Invalid part entry: {$part}", 'showParts');
        }

        $this->part = $part;
    }

    /**
     * Request day value from user
     * @return string|void
     * @throws Exception
     */
    public function getDayInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWhich day would you like to run? [{$this->daysRange}] : ");
        $input = trim(fgets(STDIN));
        if (!in_array($input, $this->days)) {
            $this->inputError("Invalid day entry: {$input}", 'showDays');
        } else {
            return $input;
        }
    }

    /**
     * Request test value from user
     * @return bool|void
     * @throws Exception
     */
    public function getTestInput()
    {
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nDo you want to use test data? [y/n] : ");
        $input = trim(fgets(STDIN));

        switch ($input) {
            case 'y':
            case 'yes':
                print EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, ">> Using test data\n");
                return true;

            case 'n':
            case 'no':
                print EscapeColors::fg_color(LOG_DEBUG_FG_COLOR, ">> Using real data\n");
                return false;

            default:
                $this->inputError('Invalid entry');
        }
    }

    /**
     * Get restart value from user
     * @return void
     * @throws Exception
     */
    public function getRestartInput()
    {
        print EscapeColors::fg_color(DIVISION_FG_COLOR, "\n" . str_repeat('▪', 90) . "\n");
        print EscapeColors::fg_color(PROMPT_FG_COLOR, "\nWould you like to run again? [y/n] : ");
        $input = strtolower(trim(fgets(STDIN)));

        switch ($input) {
            case 'y':
            case 'yes':
                $this->start();
                break;

            case 'n':
            case 'no':
                exit(0);

            default:
                $this->inputError('Invalid entry');
        }
    }

    /**
     * Gracefully handle a user input error
     * @param $msg
     * @param $calloutFunc
     * @throws Exception
     */
    public function inputError($msg, $calloutFunc = null)
    {
        $func = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'];

        $this->showError($msg, $func);

        if (!is_null($calloutFunc) && method_exists($this, $calloutFunc) && $this->mode === 'command') {
            $this->$calloutFunc();
        }

        if ($this->mode === 'command') {
            $this->start();
        } else {
            $this->$func();
        }
    }

    public function showError($msg, $func)
    {
        //print EscapeColors::fg_color(LOG_ERROR_FG_COLOR, ">> {$msg} [{$func}]\n");
        print EscapeColors::fg_color(LOG_ERROR_FG_COLOR, ">> {$msg}\n");
    }

    function beep()
    {
        if ($this->audible) {
            fprintf(STDOUT, '%s', "\x07");
        }
    }
}

$run = new Run();
$run->start();
