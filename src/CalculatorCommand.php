<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator;

use Nefedov89\NeufferCalculator\Operations\CalculatorOperationDivision;
use Nefedov89\NeufferCalculator\Operations\CalculatorOperationMinus;
use Nefedov89\NeufferCalculator\Operations\CalculatorOperationMultiply;
use Nefedov89\NeufferCalculator\Operations\CalculatorOperationPlus;
use const true, false, null;
use function getopt, sprintf, in_array, file_exists, unlink, fopen, fclose,
    is_readable, class_exists, fgetcsv, implode;

/**
 * Class CalculatorCommand
 *
 * @package Nefedov89\NeufferCalculator
 */
class CalculatorCommand
{
    private const ACTION_ARG_SHORT = 'a';

    private const FILE_ARG_SHORT = 'f';

    private const ACTION_ARG_LONG = 'action';

    private const FILE_ARG_LONG = 'file';

    private const LOG_FILE = 'storage/log.txt';

    private const RESULT_FILE = 'storage/result.csv';

    private $resultFileHandler;

    private $logFileHandler;

    /** @var array */
    private static $calculatorOperationsMap = [
        'plus'     => CalculatorOperationPlus::class,
        'minus'    => CalculatorOperationMinus::class,
        'multiply' => CalculatorOperationMultiply::class,
        'division' => CalculatorOperationDivision::class,
    ];

    /** @var array */
    private $allowedActions = [
        'plus',
        'minus',
        'multiply',
        'division',
    ];

    /** @var string */
    private $action;

    /** @var string */
    private $file;

    /**
     * CalculatorCommand constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->prepareFiles();
        $this->prepareFileHandlers();
        $this->setArguments();
        $this->setCalculatorOperationsPool();
    }

    /**
     * CalculatorCommand destructor.
     */
    public function __destruct()
    {
        $this->closeFileHandlers();
    }

    /**
     * Execute main functionality.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function execute(): void
    {
        $this->validateArguments();

        $operation = CalculatorOperationsPool::getOperation($this->action);

        if ($operation) {
            $startMessage = "Started {$this->action} operation";
            $finishMessage = "Finished {$this->action} operation";

            $this->logInfo($startMessage);
            $this->successInfo($startMessage);

            $handle = fopen($this->file,'r');

            while (($line = fgetcsv($handle)) !== false) {
                list($value1, $value2) = $operation->prepareValues($line[0]);

                $result = $operation->countResult($value1, $value2);

                if ($operation->isResultValid($result)) {
                    $this->writeSuccessResult($value1, $value2, $result);
                } else {
                    $this->writeWrongResult($value1, $value2);
                }
            }

            $this->logInfo($finishMessage);
            $this->successInfo($finishMessage);
        }
    }

    /**
     * Check and delete main files before execution.
     *
     * @return void
     */
    private function prepareFiles(): void
    {
        $filesToPrepare = [
            self::LOG_FILE,
            self::RESULT_FILE,
        ];

        foreach ($filesToPrepare as $fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    /**
     * Prepare file handlers to writing.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function prepareFileHandlers(): void
    {
        $this->logFileHandler = fopen(self::LOG_FILE, 'a+');

        if ($this->logFileHandler === false) {
            throw new \Exception('Log File cannot be open for writing');
        }

        $this->resultFileHandler = fopen(self::RESULT_FILE, 'a+');

        if ($this->resultFileHandler === false) {
            throw new \Exception('Result File cannot be open for writing');
        }
    }

    /**
     * Close opened file handlers.
     *
     * @return void
     */
    private function closeFileHandlers(): void
    {
        fclose($this->logFileHandler);
        fclose($this->resultFileHandler);
    }

    /**
     * Set command arguments.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function setArguments(): void
    {
        $shortOptions = sprintf(
            '%s:%s:',
            self::ACTION_ARG_SHORT,
            self::FILE_ARG_SHORT
        );
        $longOptions = [
            self::ACTION_ARG_LONG.':',
            self::FILE_ARG_LONG.':',
        ];

        $arguments = getopt($shortOptions, $longOptions);

        if ($arguments === false) {
            throw new \Exception('Invalid input format');
        }

        $this->action = $this->getArgument(
            $arguments,
            self::ACTION_ARG_SHORT,
            self::ACTION_ARG_LONG
        );

        $this->file = $this->getArgument(
                $arguments,
                self::FILE_ARG_SHORT,
                self::FILE_ARG_LONG
            ) ?? 'not_exists.csv';
    }

    /**
     * Get particular command argument.
     *
     * @param array $arguments
     * @param string $shortName
     * @param string $longName
     *
     * @return null|string
     */
    private function getArgument(
        array $arguments,
        string $shortName,
        string $longName
    ): ?string {
        switch (true) {
            case isset($arguments[$shortName]):
                return $arguments[$shortName];
                break;
            case isset($arguments[$longName]):
                return $arguments[$longName];
                break;
            default:
                return null;
        }
    }

    /**
     * Validate arguments.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function validateArguments(): void
    {
        // Validate action
        if (!in_array($this->action, $this->allowedActions)) {
            throw new \Exception('Wrong action is selected');
        }

        // Validate file.
        if ($this->file === null || !file_exists($this->file)) {
            throw new \Exception('Please define file with data');
        }

        if (!is_readable($this->file)) {
            throw new \Exception('We have not rights to read this file');
        }
    }

    /**
     * Fill calculator operation pool with data.
     *
     * @return void
     */
    private function setCalculatorOperationsPool(): void
    {
        foreach (self::$calculatorOperationsMap as $action => $class) {
            if (class_exists($class)) {
                CalculatorOperationsPool::setOperation($action, new $class());
            }
        }
    }

    /**
     * Write messages in log file.
     *
     * @param string $message
     *
     * @return void
     */
    private function logInfo(string $message): void
    {
        fwrite($this->logFileHandler, $message."\r\n");
    }

    /**
     * Write message in result file.
     *
     * @param string $message
     *
     * @return void
     */
    private function successInfo(string $message): void
    {
        fwrite($this->resultFileHandler, $message."\r\n");
    }

    /**
     * Prepare info and save it in result file.
     *
     * @param int $value1
     * @param int $value2
     * @param float $result
     *
     * @return void
     */
    private function writeSuccessResult(int $value1, int $value2, float $result): void
    {
        $this->successInfo(implode(';', [$value1, $value2, $result]));
    }

    /**
     * Write in logs if numbers give wrong result.
     *
     * @param int $value1
     * @param int $value2
     */
    private function writeWrongResult(int $value1, int $value2): void
    {
        $this->logInfo("Numbers {$value1} and {$value2} are wrong");
    }
}