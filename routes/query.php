<?php
declare(strict_types=1);

use App\Controllers\Query\FactoryController;
use Lsr\Core\Routing\Route;

$query = Route::group('query');

$factory = $query->group('factory');
$factoryId = $factory->group('{id}');

$factory->get('', [FactoryController::class, 'find']);
$factory->get('stopped', [FactoryController::class, 'stoppedFactories']);
$factoryId->get('', [FactoryController::class, 'show']);
