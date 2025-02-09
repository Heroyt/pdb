<?php

declare(strict_types=1);

namespace App\Models;

use Lsr\Core\App;
use Lsr\Core\Caching\Cache;
use Lsr\Core\Models\Attributes\ManyToOne;
use Lsr\Core\Models\Attributes\PrimaryKey;
use Lsr\Core\Models\Model;
use OpenApi\Attributes as OA;

#[PrimaryKey('id_storage'), OA\Schema]
class FactoryStorage extends Model
{
    public const string TABLE = 'factory_storage';

    #[ManyToOne(localKey: 'id_factory'), OA\Property]
    public Factory $facility;
    #[ManyToOne, OA\Property]
    public Material $material;
    #[OA\Property]
    public int $quantity = 0;

    public function getCacheTags(): array {
        $tags = parent::getCacheTags();
        $tags[] = Material::TABLE . '/' . $this->material->id;
        $tags[] = Factory::TABLE . '/' . $this->facility->id;
        $tags[] = Factory::TABLE . '/' . $this->facility->id . '/storage';
        return $tags;
    }

    public function clearCache(): void {
        parent::clearCache();

        $cache = App::getService('cache');
        assert($cache instanceof Cache);
        $cache->clean(
            [
            $cache::Tags => [
              Factory::TABLE . '/' . $this->facility->id,
              Factory::TABLE . '/' . $this->facility->id . '/storage',
            ],
            ]
        );
    }
}
