<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator\Operations;
use function explode, trim, intval;

/**
 * Class CalculatorOperation
 */
abstract class CalculatorOperation
{
    /**
     * Prepare numbers before action, explode it from csv string.
     *
     * @param string $line
     *
     * @return array
     */
    public function prepareValues(string $line): array
    {
        $line = explode(';', $line);
        $value1 = $this->prepareNumber($line[0]);
        $value2 = $this->prepareNumber($line[1]);

        return [$value1, $value2];
    }

    /**
     * Prepare number before action.
     *
     * @param string $value
     *
     * @return int
     */
    public function prepareNumber(string $value): int
    {
        return intval(trim($value));
    }

    /**
     * Validate if result is valid.
     *
     * @param float $result
     *
     * @return bool
     */
    public function isResultValid(float $result): bool
    {
        return $result > 0;
    }
}