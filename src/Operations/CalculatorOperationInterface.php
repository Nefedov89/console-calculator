<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator\Operations;

/**
 * Interface CalculatorOperationInterface
 *
 * @package Nefedov89\NeufferCalculator\Operations
 */
interface CalculatorOperationInterface
{
    /**
     * Count result of each operation.
     *
     * @param int $value1
     * @param int $value2
     *
     * @return float
     */
    public function countResult(int $value1, int $value2): float;
}