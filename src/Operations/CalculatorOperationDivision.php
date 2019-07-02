<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator\Operations;
use function round;

/**
 * Class CalculatorOperationDivision
 *
 * @package Nefedov89\NeufferCalculator\Operations
 */
class CalculatorOperationDivision extends CalculatorOperation implements CalculatorOperationInterface
{
    /**
     * @inheritdoc
     *
     * @param int $value1
     * @param int $value2
     *
     * @return float
     */
    public function countResult(int $value1, int $value2): float
    {
        return $value2 !== 0 ? round($value1 / $value2, 2) : 0;
    }
}