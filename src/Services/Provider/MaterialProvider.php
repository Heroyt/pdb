<?php

declare(strict_types=1);

namespace App\Services\Provider;

use App\EventStore\Events\Material\CreateEvent;
use App\EventStore\Events\Material\DeleteEvent;
use App\EventStore\Events\Material\UpdateEvent;
use App\EventStore\Streams;
use App\Exceptions\ModelCreationException;
use App\Exceptions\ModelDeleteException;
use App\Models\Material;
use App\Request\Material\UpdateRequest;
use Dibi\DriverException;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ValidationException;

readonly class MaterialProvider
{
    public function __construct(
        private Streams $streams,
    ) {
    }

    /**
     * @param  non-empty-string  $name
     * @param  int<1,max>  $size
     * @param  bool  $wildcard
     * @return Material
     * @throws DriverException
     * @throws ModelCreationException
     * @throws ValidationException
     */
    public function createMaterial(string $name, int $size, bool $wildcard = false): Material {
        $material = new Material();
        $material->name = $name;
        $material->size = $size;
        $material->wildcard = $wildcard;
        DB::getConnection()->begin();
        if (!$material->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to save material to database');
        }
        $result = $this->streams->appendEvent(
            CreateEvent::fromMaterial($material),
            $material::TABLE . '_' . $material->id
        );
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $material;
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws ValidationException
     */
    public function updateMaterial(UpdateRequest $request): Material {
        $changes = $request->getChanges();
        if (empty($changes)) {
            return $request->entity; // No changes
        }
        DB::getConnection()->begin();
        $event = UpdateEvent::fromMaterial($request->entity);
        foreach ($changes as $property => $value) {
            $event->{$property} = $value;
        }
        $material = $request->apply();
        if (!$material->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to save material to database');
        }
        $result = $this->streams->appendEvent($event, $material::TABLE . '_' . $material->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelCreationException($result->status->details);
        }
        DB::getConnection()->commit();
        return $material;
    }

    /**
     * @throws DriverException
     * @throws ModelDeleteException
     */
    public function deleteMaterial(Material $material): void {
        DB::getConnection()->begin();
        if (!$material->delete()) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException('Failed to delete material: ' . $material->name);
        }
        $result = $this->streams->appendEvent(DeleteEvent::fromMaterial($material), $material::TABLE . '_' . $material->id);
        if (!$result->success) {
            DB::getConnection()->rollback();
            throw new ModelDeleteException($result->status->details);
        }
        DB::getConnection()->commit();
    }
}
