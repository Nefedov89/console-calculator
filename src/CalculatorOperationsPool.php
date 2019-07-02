<?php

declare(strict_types = 1);

namespace Nefedov89\NeufferCalculator;

use Nefedov89\NeufferCalculator\Operations\CalculatorOperationInterface;

/**
 * Class CalculatorOperationsPool
 *
 * @package Nefedov89\NeufferCalculator
 */
class CalculatorOperationsPool
{
    /** @var array */
    private static $operations = [];

    /**
     * Set calculator operation to pool.
     *
     * @param string $key
     * @param CalculatorOperationInterface $operation
     *
     * @return void
     */
    public static function setOperation(
        string $key,
        CalculatorOperationInterface $operation
    ): void {
        self::$operations[$key] = $operation;
    }

    /**
     * Get calculator operation from pool.
     *
     * @param string $key
     *
     * @return CalculatorOperationInterface|null
     */
    public static function getOperation(string $key): ?CalculatorOperationInterface
    {
        return isset(self::$operations[$key]) ? self::$operations[$key] : null;
    }
}