<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\Direction;
use App\Models\Factory;
use Lsr\Core\Exceptions\ModelNotFoundException;
use OpenApi\Attributes as OA;

/**
 * @phpstan-consistent-constructor
 */
#[OA\Schema(schema: 'FactoryFullDto')]
class FactoryFull
{
    private ?Factory $factory = null;

    /** @var Storage[] */
    #[OA\Property]
    public array $storage = [];
    #[OA\Property]
    public Process $process;
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public int $storageCapacity,
    ) {
    }

    public static function fromFactory(Factory $factory): static {
        $self = new static($factory->id, $factory->name, $factory->storageCapacity);
        $self->factory = $factory;
        foreach ($factory->storage as $storage) {
            $self->storage[] = Storage::fromFactoryStorage($storage);
        }
        $self->process = new Process();
        foreach ($factory->processes as $process) {
            if ($process->type === Direction::IN) {
                $self->process->addInput(ProcessPart::fromProcess($process));
            } else {
                $self->process->addOutput(ProcessPart::fromProcess($process));
            }
        }
        return $self;
    }

    public function addStorage(Storage $storage): void {
        $this->storage[] = $storage;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getFactory(): Factory {
        $this->factory ??= Factory::get($this->id);
        return $this->factory;
    }
}
