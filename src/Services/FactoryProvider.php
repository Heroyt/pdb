<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ModelCreationException;
use App\Models\Factory;
use Dibi\DriverException;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\ValidationException;
use Lsr\Logging\Logger;
use Throwable;

readonly class FactoryProvider
{
    /**
     * @param  ClientInterface<SummarizedResult<BoltResult>>  $client
     */
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @throws ModelCreationException
     * @throws ValidationException
     * @throws DriverException
     * @throws Throwable
     */
    public function createFactory(string $name, int $capacity = 50): Factory {
        $factory = new Factory();
        $factory->name = $name;
        $factory->storageCapacity = $capacity;
        return $this->updateFactory($factory);
    }

    /**
     * @throws DriverException
     * @throws ModelCreationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function updateFactory(Factory $factory): Factory {
        DB::getConnection()->begin();
        if (!$factory->save()) {
            DB::getConnection()->rollback();
            throw new ModelCreationException('Failed to create factory');
        }
        try {
            $this->client->writeTransaction(fn($tsx) => $this->createFactoryNode($tsx, $factory));
        } catch (Throwable $e) {
            DB::getConnection()->rollback();
            throw $e;
        }
        DB::getConnection()->commit();
        return $factory;
    }

    /**
     * @param  TransactionInterface<SummarizedResult<BoltResult>>  $tsx
     */
    public function createFactoryNode(TransactionInterface $tsx, Factory $factory): void {
        $result = $tsx->run(
            'MERGE (f:Factory {id: $id}) SET f.name = $name return f',
            ['id' => $factory->id, 'name' => $factory->name]
        );
        new Logger(LOG_DIR, 'neo4j')->debug('Result:', $result->jsonSerialize());
    }
}
