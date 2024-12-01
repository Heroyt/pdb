<?php

declare(strict_types=1);

use App\Controllers\Command\ConnectionController;
use App\Controllers\Command\FactoryController;
use App\Controllers\Command\MaterialController;
use App\Controllers\Command\ProcessController;
use Lsr\Core\Routing\Route;

$command = Route::group('command');

$factory = $command->group('factory');
$factoryId = $factory->group('{id}');

$factory->post('', [FactoryController::class, 'create']);
$factoryId->put('', [FactoryController::class, 'update']);
$factoryId->delete('', [FactoryController::class, 'delete']);
$factoryId->put('storage', [FactoryController::class, 'updateStorage']);
$factoryId->post('process', [ProcessController::class, 'create']);

$material = $command->group('material');
$materialId = $material->group('{id}');

$material->post('', [MaterialController::class, 'create']);
$materialId->put('', [MaterialController::class, 'update']);
$materialId->delete('', [MaterialController::class, 'delete']);

$connection = $command->group('connection');
$connectionId = $connection->group('{id}');

$connection->post('', [ConnectionController::class, 'create']);
$connectionId->put('', [ConnectionController::class, 'update']);
$connectionId->delete('', [ConnectionController::class, 'delete']);
$connectionId->put('storage', [ConnectionController::class, 'updateStorage']);
$connectionId->put('storage-max', [ConnectionController::class, 'updateStorageMax']);
$connectionId->post('assign', [ConnectionController::class, 'assign']);
$connectionId->post('unassign', [ConnectionController::class, 'unassign']);
$connectionId->post('activate', [ConnectionController::class, 'activate']);
$connectionId->post('deactivate', [ConnectionController::class, 'deactivate']);

$process = $command->group('process');
$processId = $process->group('{id}');

$process->delete('', [ProcessController::class, 'delete']);
