<?php

/*
 * bootstrapping the test suite with composer
 */

if (!$loader = @include __DIR__ . '/../vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:' . PHP_EOL .
            'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
            'php composer.phar install --dev' . PHP_EOL);
}

$loader->addPsr4('tests\\Trismegiste\\SerialIcer\\', __DIR__, true);

require_once __DIR__ . '/fixtures.php';
