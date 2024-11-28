<?php

declare(strict_types=1);

use App\Controllers\Command\FactoryController;
use Lsr\Core\Routing\Route;

$command = Route::group('command');

$factory = $command->group('factory');
$factoryId = $factory->group('{id}');

$factory->post('', [FactoryController::class, 'create']);
$factoryId->put('', [FactoryController::class, 'update']);
$factoryId->delete('', [FactoryController::class, 'delete']);
$factoryId->put('storage', [FactoryController::class, 'updateStorage']);
