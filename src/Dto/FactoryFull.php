<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\Direction;
use App\Models\Factory;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'FactoryFullDto')]
class FactoryFull
{

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
    ){}

    public static function fromFactory(Factory $factory): self {
        $self = new self($factory->id, $factory->name, $factory->storageCapacity);
        foreach ($factory->storage as $storage) {
            $self->storage[] = Storage::fromFactoryStorage($storage);
        }
        $self->process = new Process();
        foreach ($factory->processes as $process) {
             if ($process->type === Direction::IN) {
                 $self->process->addInput(ProcessPart::fromProcess($process));
             }
             else {
                 $self->process->addOutput(ProcessPart::fromProcess($process));
             }
        }
        return $self;
    }

    public function addStorage(Storage $storage): void {
        $this->storage[] = $storage;
    }

}