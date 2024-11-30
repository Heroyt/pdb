<?php

declare(strict_types=1);

use App\Controllers\Query\FactoryController;
use App\Controllers\Query\MaterialController;
use App\Controllers\Query\PathController;
use Lsr\Core\Routing\Route;

$query = Route::group('query');

$factory = $query->group('factory');
$factoryId = $factory->group('{id}');

$factory->get('', [FactoryController::class, 'find']);
$factory->get('stopped', [FactoryController::class, 'stoppedFactories']);
$factory->get('running', [FactoryController::class, 'runningFactories']);
$factoryId->get('', [FactoryController::class, 'show']);

$query->get('path', [PathController::class, 'findShortestPaths']);

$material = $query->group('material');
$materialId = $material->group('{id}');

$material->get('', [MaterialController::class, 'find']);
$materialId->get('', [MaterialController::class, 'show']);