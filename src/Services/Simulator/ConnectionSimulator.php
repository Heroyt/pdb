<?php
declare(strict_types=1);

namespace App\Services\Simulator;

use App\EventStore\Event;
use App\EventStore\Events\Connection\ActivateEvent;
use App\EventStore\Events\SimulationEvent;
use App\EventStore\Streams;
use App\Models\Connection;
use App\Request\Connection\ActivateRequest;
use App\Request\Connection\DeactivateRequest;
use App\Request\Connection\UpdateStorageRequest as UpdateConnectionStorageRequest;
use App\Request\Factory\UpdateStorageRequest;
use App\Services\Provider\ConnectionProvider;
use App\Services\TaskProducer;
use App\Tasks\Connection\ActivateConnection;
use App\Tasks\Connection\DeactivateConnection;
use App\Tasks\Connection\UpdateConnectionStorage;
use App\Tasks\Factory\UpdateFactoryStorage;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectionSimulator implements Simulator
{

    public function __construct(
      private readonly TaskProducer $taskProducer,
      private readonly Streams $streams,
      private readonly ConnectionProvider $connectionProvider,
    ) {}

    public function simulate(int $currentStep, OutputInterface $output) : void {
        $output->writeln('Simulating connections...', OutputInterface::VERBOSITY_VERBOSE);
        $this->processActiveConnections($currentStep, $output);
        $this->processAssignedConnections($output);
    }

    private function processActiveConnections(int $currentStep, OutputInterface $output) : void {
        // Find all active connection
        $activeConnections = Connection::query()->where('active = 1')->get();
        foreach ($activeConnections as $connection) {
            // Find the last simulation step before the connection was set active
            /** @var Streams\ReadResult<Event> $result */
            $result = $this->streams->query()
                                    ->stream(Connection::TABLE.'_'.$connection->id)
                                    ->backwards()
                                    ->send();
            $position = 0;
            foreach ($result->get() as $eventWrapper) {
                if ($eventWrapper->event instanceof ActivateEvent) {
                    $position = $eventWrapper->commitPosition;
                    break;
                }
            }

            // Find last SimulationEvent before the ActivateEvent
            /** @var Streams\ReadResult<SimulationEvent> $result */
            $result = $this->streams->query()
                                    ->type(SimulationEvent::class)
                                    ->position($position)
                                    ->backwards()
                                    ->limit(1)
                                    ->send();

            $step = 0;
            foreach ($result->get() as $eventWrapper) {
                $step = $eventWrapper->event->step;
            }

            $travelingFor = $currentStep - $step;
            if ($travelingFor < $connection->speed) {
                // Still on its way
                continue;
            }

            // Find connected factories
            $factoryConnection = $this->connectionProvider->getFactoryConnectionForConnection($connection);
            assert($factoryConnection !== null);

            // Unload all possible storage
            $factory = $factoryConnection->end;
            $remainingStorage = $factory->getRemainingStorageCapacity();
            $unloadedSize = 0;

            $unloadedEverything = true;
            foreach ($connection->storage as $storage) {
                $materialSize = $storage->material->size;
                $quantity = $storage->quantity;
                $totalSize = $quantity * $materialSize;
                if ($totalSize > $remainingStorage) {
                    $unloadedEverything = false;

                    // Unload only what is possible
                    $quantity = (int) floor($remainingStorage / $materialSize);
                    $totalSize = $quantity * $materialSize;
                }
                // Update remaining storage
                $remainingStorage -= $totalSize;
                $unloadedSize += $totalSize;

                // Update connection storage
                $request = new UpdateConnectionStorageRequest($storage);
                $request->material = $storage->material;
                $request->quantity = -$quantity;
                $this->taskProducer->plan(UpdateConnectionStorage::class, $request);

                // Update factory storage
                $factoryStorage = $factory->getOrCreateStorageForMaterial($storage->material);
                $request = new UpdateStorageRequest($factoryStorage);
                $request->material = $storage->material;
                $request->quantity = $quantity;
                $this->taskProducer->plan(UpdateFactoryStorage::class, $request);
            }

            $output->writeln(
              sprintf(
                'Unloaded connection (id: %d) at %s (%d) - size: %d',
                $connection->id,
                $factory->name,
                $factory->id,
                $unloadedSize
              ),
              OutputInterface::VERBOSITY_VERBOSE
            );

            if ($unloadedEverything) {
                $output->writeln(
                  'Unloaded everything - deactivating connection',
                  OutputInterface::VERBOSITY_VERBOSE
                );
                $this->taskProducer->plan(DeactivateConnection::class, new DeactivateRequest($connection->id));
            }
        }
    }

    private function processAssignedConnections(OutputInterface $output) : void {
        // Find assigned and not active connections
        $connections = Connection::query()->where('active = 0 AND assigned = 1')->get();
        foreach ($connections as $connection) {
            // Find connected factories
            $factoryConnection = $this->connectionProvider->getFactoryConnectionForConnection($connection);
            assert($factoryConnection !== null);

            $factory = $factoryConnection->start;
            $remainingStorageCapacity = $connection->getRemainingStorageCapacity();
            $currentStorage = $connection->getFilledStorageCapacity();

            // Load connection storage as much as possible
            foreach ($connection->storage as $storage) {
                if ($storage->maxQuantity < 1) {
                    continue; // Loading for this material is not set
                }
                $factoryStorage = $factory->getStorageForMaterial($storage->material);
                if ($factoryStorage === null) {
                    continue; // Material is not stocked in factory
                }

                // Calculate maximum storable quantity of the material
                $quantity = min(
                  $factoryStorage->quantity, // What is in store in factory
                  $storage->maxQuantity,     // What is set as maximum quantity on connection
                  (int) floor(
                    $remainingStorageCapacity / $storage->material->size
                  ) // What can fit in connection storage
                );

                if ($quantity < 1) {
                    continue; // Cannot load anything
                }

                $size = $quantity * $storage->material->size;
                $currentStorage += $size;
                $remainingStorageCapacity -= $size;

                // Update connection storage
                $request = new UpdateConnectionStorageRequest($storage);
                $request->material = $storage->material;
                $request->quantity = $quantity;
                $this->taskProducer->plan(UpdateConnectionStorage::class, $request);

                // Update factory storage
                $request = new UpdateStorageRequest($factoryStorage);
                $request->material = $storage->material;
                $request->quantity = -$quantity;
                $this->taskProducer->plan(UpdateFactoryStorage::class, $request);
            }

            if ($currentStorage < 1) {
                // Don't activate connection if it has nothing stored
                continue;
            }
            
            $output->writeln(
              sprintf(
                'Loaded and activated connection (id: %d) at %s (%d) - size: %d',
                $connection->id,
                $factory->name,
                $factory->id,
                $currentStorage
              ),
              OutputInterface::VERBOSITY_VERBOSE
            );

            // Start transport
            $this->taskProducer->plan(ActivateConnection::class, new ActivateRequest($connection->id));
        }
    }
}