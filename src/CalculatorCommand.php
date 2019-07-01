<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator;

use const true, false, null;
use function getopt, sprintf, in_array, file_exists, unlink, fopen, fclose,
    is_readable;

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
    }

    /**
     * Check and delete main files before execution.
     *
     * @return void
     */
    private function prepareFiles() : void
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
    private function prepareFileHandlers() : void
    {
        $this->logFileHandler = fopen(self::LOG_FILE, 'a+');

        if($this->logFileHandler === false) {
            throw new \Exception('Log File cannot be open for writing');
        }

        $this->resultFileHandler = fopen(self::RESULT_FILE, 'a+');

        if($this->resultFileHandler === false) {
            throw new \Exception('Result File cannot be open for writing');
        }
    }

    /**
     * Close opened file handlers.
     *
     * @return void
     */
    private function closeFileHandlers() : void
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
}