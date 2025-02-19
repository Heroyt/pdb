<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\OneToMany;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\LoadingType;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_connection'), OA\Schema]
class Connection extends Model
{
    public const string TABLE = 'connections';

    #[OA\Property(description: 'If a connection is assigned, it is set to transport material between factories.')]
    public bool $assigned = false;
    #[OA\Property(description: 'If a connection is active, it is currently on route between factories.')]
    public bool $active = false;
    #[OA\Property(description: 'How many simulation steps does it take.')]
    public int $speed = 1;
    #[OA\Property]
    public int $storageCapacity = 10;


    /** @var ConnectionStorage[] */
    #[
      OneToMany(foreignKey: 'id_connection', localKey: 'id_connection', class: ConnectionStorage::class, loadingType: LoadingType::LAZY),
      OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/ConnectionStorage'))
    ]
    public array $storage {
        get {
            if (!isset($this->storage)) {
                $this->storage = ConnectionStorage::query()->where('id_connection = %i', $this->id)->get();
            }
            return $this->storage;
        }
        /**
         * @param  Process[]  $value
         */
        set(array $value) => $this->storage = $value;
    }

    public function getFilledStorageCapacity(): int {
        return array_reduce(
          $this->storage,
          static fn(int $value, ConnectionStorage $storage) => $value + ($storage->quantity * $storage->material->size),
          0
        );
    }

    public function getRemainingStorageCapacity() : int {
        return $this->storageCapacity - $this->getFilledStorageCapacity();
    }

    public function getOrCreateStorageForMaterial(Material $material) : ConnectionStorage {
        $storage = array_find(
          $this->storage,
          static fn(ConnectionStorage $storage) => $storage->material->id === $material->id
        );
        if ($storage !== null) {
            return $storage;
        }
        $storage = new ConnectionStorage();
        $storage->connection = $this;
        $storage->material = $material;
        return $storage;
    }
}
