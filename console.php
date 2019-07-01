<?php

declare(strict_types = 1);

use Nefedov89\NeufferCalculator\CalculatorCommand;

require __DIR__.'/vendor/autoload.php';

try {
    $calculatorCommand = new CalculatorCommand();
    $calculatorCommand->execute();
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}