<?php

declare(strict_types=1);

use App\Controllers\Command\FactoryController;
use Lsr\Core\Routing\Route;

$command = Route::group('command');

$factory = $command->group('factory');

$factory->post('', [FactoryController::class, 'create']);
