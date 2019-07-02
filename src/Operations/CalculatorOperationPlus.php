<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator\Operations;

/**
 * Class CalculatorOperationPlus
 *
 * @package Nefedov89\NeufferCalculator\Operations
 */
class CalculatorOperationPlus extends CalculatorOperation implements CalculatorOperationInterface
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
        return $value1 + $value2;
    }
}