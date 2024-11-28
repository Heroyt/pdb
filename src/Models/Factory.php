<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\Models\Attributes\OneToMany;
use Lsr\Core\Models\Attributes\PrimaryKey;
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
      OneToMany(foreignKey: 'id_factory', localKey: 'id_factory', class: FactoryStorage::class),
      OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/FactoryStorage'))
    ]
    public array $storage = [];

    public function getRemainingStorageCapacity(): int {
        $filled = 0;
        foreach ($this->storage as $storage) {
            $filled += $storage->material->size * $storage->quantity;
        }
        return $this->storageCapacity - $filled;
    }
}
