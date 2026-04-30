<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$autoloadCandidates = [
    $root.'/vendor/autoload.php',
    dirname($root).'/Accessing/vendor/autoload.php',
    dirname($root).'/Interfacing/vendor/autoload.php',
    dirname($root).'/Messaging/vendor/autoload.php',
];

foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
    }
}

$orderingInterface = dirname($root).'/Ordering/src/ServiceInterface/OrderSummaryProviderInterface.php';
if (is_file($orderingInterface)) {
    require $orderingInterface;
}
