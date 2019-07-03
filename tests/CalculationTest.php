<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;

/**
 * Class CalculationTest
 */
class CalculationTest extends TestCase
{
    private const LOG_FILE = 'storage/log.txt';
    private const RESULT_FILE = 'storage/result.csv';

    private $operations = [
        'plus',
        'minus',
        'multiply',
        'division',
    ];

    /**
     * Test calculator.
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCalculator(): void
    {
        foreach ($this->operations as $operation) {
            $this->runCommand($operation);
            $this->assertFileExists(self::RESULT_FILE);
            $this->assertFileExists(self::LOG_FILE);

            // Result data.
            $resultHandle = fopen(self::RESULT_FILE, 'r');
            $resultData = [];

            while (($line = fgetcsv($resultHandle)) !== false) {
                $resultData[] = $line[0];
            }

            fclose($resultHandle);

            // Log data.
            $logHandle = fopen(self::LOG_FILE, 'r');
            $logData = [];

            while (($line = fgetcsv($logHandle, 1000, PHP_EOL)) !== false) {
                $logData[] = $line[0];
            }

            fclose($logHandle);

            // Make assertions.
            $assertResultsMap = $this->getAssertResultsMap($operation);

            if ($assertResultsMap) {
                foreach ($assertResultsMap['result'] as $index => $line) {
                    $this->assertEquals($line, $resultData[$index]);
                }

                foreach ($assertResultsMap['log'] as $index => $line) {
                    $this->assertEquals($line, $logData[$index]);
                }
            }
        }

        $this->removeFiles();
    }

    /**
     * Run command with correct args.
     *
     * @param string $operation
     *
     * @return void
     */
    private function runCommand(string $operation): void
    {
        $this->removeFiles();

        exec("php console.php -a {$operation} -f tests/data/test.csv");
    }

    /**
     * Run command with correct args.
     *
     * @return void
     */
    private function removeFiles(): void
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
     * @param string $operation
     *
     * @return array
     */
    private function getAssertResultsMap(string $operation): array
    {
        $map = [
            'plus'     => [
                'log'    => [
                    'Started plus operation',
                    'Numbers -97 and 90 are wrong',
                    'Numbers 19 and -40 are wrong',
                    'Finished plus operation',
                ],
                'result' => [
                    'Started plus operation',
                    '72;-58;14',
                    '-1;10;9',
                    '5;0;5',
                    '12;4;16',
                    'Finished plus operation',
                ],
            ],
            'minus'    => [
                'log'    => [
                    'Started minus operation',
                    'Numbers -97 and 90 are wrong',
                    'Numbers -1 and 10 are wrong',
                    'Finished minus operation',
                ],
                'result' => [
                    'Started minus operation',
                    '72;-58;130',
                    '19;-40;59',
                    '5;0;5',
                    '12;4;8',
                    'Finished minus operation',
                ],
            ],
            'multiply' => [
                'log'    => [
                    'Started multiply operation',
                    'Numbers -97 and 90 are wrong',
                    'Numbers 72 and -58 are wrong',
                    'Numbers -1 and 10 are wrong',
                    'Numbers 19 and -40 are wrong',
                    'Numbers 5 and 0 are wrong',
                    'Finished multiply operation',
                ],
                'result' => [
                    'Started multiply operation',
                    '12;4;48',
                    'Finished multiply operation',
                ],
            ],
            'division' => [
                'log'    => [
                    'Started division operation',
                    'Numbers -97 and 90 are wrong',
                    'Numbers 72 and -58 are wrong',
                    'Numbers -1 and 10 are wrong',
                    'Numbers 19 and -40 are wrong',
                    'Numbers 5 and 0 are wrong, is not allowed',
                    'Finished division operation',
                ],
                'result' => [
                    'Started division operation',
                    '12;4;3',
                    'Finished division operation',
                ],
            ],
        ];

        return isset($map[$operation]) ? $map[$operation] : [];
    }
}