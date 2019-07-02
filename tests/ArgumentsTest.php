<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;

/**
 * Class ArgumentsTest
 */
class ArgumentsTest extends TestCase
{
    /**
     * Test action argument.
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInvalidActionArgument(): void
    {
        $arguments = [
            '-a fooBar',
            '-a minus1',
            '-a plus_2',
            '-a multiply-test',
            '-a division:4',
            '-a',
            '--a',
            '-action',
            '--action test',
            '--action minus1',
            '--action plus_2',
            '--action multiply-test',
            '--action division:4',
            '--action',
            '',
        ];

        foreach ($arguments as $argument) {
            $output = exec("php console.php {$argument}");
            $this->assertEquals(
                $output,
                'Error: Wrong action is selected'
            );
        }
    }

    /**
     * Test file argument.
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInvalidFileArgument(): void
    {
        $arguments = [
            '-f fooBar',
            '-f foo.bar',
            '-f not_exists.csv',
            '-f',
            '--f',
            '-file',
            '--file test',
            '--file',
            '',
        ];

        foreach ($arguments as $argument) {
            $output = exec("php console.php -a minus {$argument}");
            $this->assertEquals(
                $output,
                'Error: Please define file with data'
            );
        }
    }
}