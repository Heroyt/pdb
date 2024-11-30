<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\OneToMany;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\LoadingType;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_factory'), OA\Schema]
class Factory extends Model
{
    public const string TABLE = 'factories';

    #[OA\Property]
    public string $name;
    #[OA\Property]
    public int $storageCapacity = 0;

    /** @var FactoryStorage[] */
    #[
      OneToMany(foreignKey: 'id_factory', localKey: 'id_factory', class: FactoryStorage::class, loadingType: LoadingType::LAZY),
      OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/FactoryStorage'))
    ]
    public array $storage {
        get {
            if (!isset($this->storage)) {
                $this->storage = FactoryStorage::query()->where('id_factory = %i', $this->id)->get();
            }
            return $this->storage;
        }
        /**
         * @param  Process[]  $value
         */
        set(array $value) => $this->storage = $value;
    }

    /** @var Process[] */
    #[
      OneToMany(foreignKey: 'id_factory', localKey: 'id_factory', class: Process::class, loadingType: LoadingType::LAZY),
      OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/Process'))
    ]
    public array $processes {
        get {
            if (!isset($this->processes)) {
                $this->processes = Process::query()->where('id_factory = %i', $this->id)->get();
            }
            return $this->processes;
        }
        /**
         * @param  Process[]  $value
         */
        set(array $value) => $this->processes = $value;
    }

    public function getFilledStorageCapacity(): int {
        return array_reduce(
          $this->storage,
          static fn(int $value, FactoryStorage $storage) => $value + ($storage->quantity * $storage->material->size),
          0
        );
    }

    public function getRemainingStorageCapacity() : int {
        return $this->storageCapacity - $this->getFilledStorageCapacity();
    }

    public function getOrCreateStorageForMaterial(Material $material) : FactoryStorage {
        $storage = $this->getStorageForMaterial($material);
        if ($storage !== null) {
            return $storage;
        }
        $storage = new FactoryStorage();
        $storage->facility = $this;
        $storage->material = $material;
        return $storage;
    }

    public function getStorageForMaterial(Material $material) : ?FactoryStorage {
        return array_find(
          $this->storage,
          static fn(FactoryStorage $storage) => $storage->material->id === $material->id
        );
    }
}
