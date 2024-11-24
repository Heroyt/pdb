<?php

/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace App\Install;

use App\Core\Info;
use Dibi\Exception;
use Dibi\Row;
use Lsr\Core\DB;
use Lsr\Core\Exceptions\CyclicDependencyException;
use Lsr\Core\Migrations\MigrationLoader;
use Lsr\Core\Models\Model;
use Lsr\Exceptions\FileException;
use Nette\Utils\AssertionException;
use ReflectionClass;
use ReflectionException;

/**
 * @version 0.3
 */
class DbInstall implements InstallInterface
{
    /** @var array{definition:string, modifications:array<string,string[]>}[] */
    public const array TABLES = [];

    /** @var array<class-string, string> */
    protected static array $classTables = [];

    /**
     * Install all database tables
     *
     * @param bool $fresh
     *
     * @return bool
     */
    public static function install(bool $fresh = false): bool {
        // Load migration files
        $loader = new MigrationLoader(ROOT . 'config/migrations.neon');
        try {
            $loader->load();
        } catch (CyclicDependencyException | FileException | \Nette\Neon\Exception | AssertionException $e) {
            echo "\e[0;31m" . $e->getMessage() . "\e[m\n" . $e->getTraceAsString() . "\n";
            return false;
        }

        $tables = $loader::transformToDto(array_merge($loader->migrations, self::TABLES));
        uasort($tables, static fn($a, $b) => ($a->order ?? 99) - ($b->order ?? 99));

        $connection = DB::getConnection();

        try {
            if ($fresh) {
                // Drop all tables in reverse order
                foreach (array_reverse($tables) as $tableName => $definition) {
                    if (class_exists($tableName)) {
                        $tableName = static::getTableNameFromClass($tableName);
                        if ($tableName === null) {
                            continue;
                        }
                    }
                    $connection->query("DROP TABLE IF EXISTS %n;", $tableName);
                }
            }

            // Create tables
            foreach ($tables as $tableName => $info) {
                if (class_exists($tableName)) {
                    $tableName = static::getTableNameFromClass($tableName);
                    if ($tableName === null) {
                        continue;
                    }
                }
                echo 'Creating table ' . $tableName . "\n";
                $definition = $info->definition;
                $connection->query("CREATE TABLE IF NOT EXISTS %n $definition", $tableName);
            }

            // Update tables
            if (!$fresh) {
                /** @var array<string,string> $tableVersions */
                $tableVersions = (array)Info::get('db_version', []);

                // Update all tables if there have been any changes to the tables
                foreach ($tables as $tableName => $info) {
                    if (class_exists($tableName)) {
                        $tableName = static::getTableNameFromClass($tableName);
                        if ($tableName === null) {
                            continue;
                        }
                    }
                    $currTableVersion = $tableVersions[$tableName] ?? '0.0';
                    $maxVersion = $currTableVersion;
                    foreach ($info->modifications as $version => $queries) {
                        // Check versions
                        if ($version !== 'always') {
                            if (version_compare($currTableVersion, $version) > 0) {
                                // Skip if this version have already been processed
                                continue;
                            }
                            if (version_compare($maxVersion, $version) < 0) {
                                $maxVersion = $version;
                            }
                        }

                        // Run ALTER TABLE queries for current version
                        foreach ($queries as $query) {
                            echo 'Altering table: ' . $tableName . ' - ' . $query . PHP_EOL;
                            try {
                                $connection->query("ALTER TABLE %n $query;", $tableName);
                            } catch (Exception $e) {
                                if ($e->getCode() === 1060 || $e->getCode() === 1061) {
                                    // Duplicate column <-> already created
                                    continue;
                                }
                                throw $e;
                            }
                        }
                    }
                    $tableVersions[$tableName] = $maxVersion;
                }

                // Update table version cache
                try {
                    Info::set('db_version', $tableVersions);
                } catch (Exception) {
                }
            }

            // Check indexes and foreign keys
            foreach ($tables as $tableName => $info) {
                if (class_exists($tableName)) {
                    $tableName = static::getTableNameFromClass($tableName);
                    if ($tableName === null) {
                        continue;
                    }
                }

                $indexNames = ['PRIMARY'];

                // Check indexes
                foreach ($info->indexes as $index) {
                    if ($index->pk || count($index->columns) < 1) {
                        continue;
                    }

                    $indexNames[] = $index->name;

                    // Check current indexes
                    $indexes = $connection->query("SHOW INDEX FROM %n WHERE key_name = %s;", $tableName, $index->name)
                                          ->fetchAll();
                    if (!empty($indexes)) {
                        // Index already exists
                        continue;
                    }
                    $columns = [];
                    for ($i = 0, $iMax = count($index->columns); $i < $iMax; $i++) {
                        $columns[] = '%n';
                    }
                    echo 'Creating ' . ($index->unique ? 'UNIQUE ' : '') . 'index on: ' . $tableName . ' - ' . $index->name . ' (' . implode(', ', $index->columns) . ')' . PHP_EOL;
                    $connection->query(
                        'CREATE ' . ($index->unique ? 'UNIQUE ' : '') . 'INDEX %n ON %n (' . implode(',', $columns) . ');',
                        $index->name,
                        $tableName,
                        ...$index->columns,
                    );
                }

                // Check foreign keys
                foreach ($info->foreignKeys as $foreignKey) {
                    $refTable = $foreignKey->refTable;
                    if (class_exists($refTable)) {
                        $refTable = static::getTableNameFromClass($refTable);
                        if ($refTable === null) {
                            continue;
                        }
                    }

                    $indexNames[] = $foreignKey->column;

                    echo 'Checking foreign keys for relation ' . $tableName . '.' . $foreignKey->column . '->' . $refTable . '.' . $foreignKey->refColumn . PHP_EOL;

                    // Check current foreign keys
                    $fks = $connection
                      ->select('CONSTRAINT_NAME')
                      ->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
                      ->where('REFERENCED_TABLE_SCHEMA = (SELECT DATABASE())')
                      ->where('TABLE_NAME = %s', $tableName)
                      ->where('COLUMN_NAME = %s', $foreignKey->column)
                      ->where('REFERENCED_TABLE_NAME = %s AND REFERENCED_COLUMN_NAME = %s', $refTable, $foreignKey->refColumn)
                      ->fetchPairs();
                    $count = count($fks);
                    if ($count === 1) {
                        // FK already exists
                        continue;
                    }
                    if ($count > 1) {
                        echo 'Multiple foreign keys found for relation ' . $tableName . '.' . $foreignKey->column . '->' . $refTable . '.' . $foreignKey->refColumn . ' - ' . implode(', ', $fks) . PHP_EOL;
                        // FK already exists, but is duplicated
                        array_shift($fks); // Remove first element
                        // Drop any duplicate foreign key
                        foreach ($fks as $fkName) {
                            try {
                                echo 'DROPPING foreign key on: ' . $tableName . ' - ' . $fkName . PHP_EOL;
                                $connection->query('ALTER TABLE %n DROP FOREIGN KEY %n;', $tableName, $fkName);
                            } catch (Exception $e) {
                                echo $e->getMessage() . PHP_EOL;
                            }
                        }
                        continue;
                    }

                    // Create new foreign key
                    echo 'Creating foreign key on: ' . $tableName . ' - ' . $foreignKey->column . '->' . $refTable . '.' . $foreignKey->refColumn . PHP_EOL;
                    $connection->query(
                        'ALTER TABLE %n ADD FOREIGN KEY (%n) REFERENCES %n (%n) ON DELETE %SQL ON UPDATE %SQL;',
                        $tableName,
                        $foreignKey->column,
                        $refTable,
                        $foreignKey->refColumn,
                        $foreignKey->onDelete,
                        $foreignKey->onUpdate,
                    );
                }

                // DROP all undefined indexes
                echo 'DROPPING indexes on ' . $tableName . ' other then: ' . implode(', ', $indexNames) . PHP_EOL;
                $indexes = $connection->query("SHOW INDEX FROM %n WHERE key_name NOT IN %in;", $tableName, $indexNames)
                                      ->fetchAll();
                /** @var Row $row */
                foreach ($indexes as $row) {
                    try {
                        echo 'DROPPING index on: ' . $tableName . ' - ' . $row->Key_name . PHP_EOL;
                        $connection->query('DROP INDEX %n ON %n;', $row->Key_name, $tableName);
                    } catch (Exception $e) {
                        echo $e->getMessage() . PHP_EOL;
                    }
                }
            }
        } catch (Exception $e) {
            echo "\e[0;31m" . $e->getMessage() . "\e[m\n" . $e->getSql() . "\n";
            return false;
        }

        return true;
    }

    /**
     * Get a table name for a Model class
     *
     * @param class-string $className
     *
     * @return string|null
     */
    protected static function getTableNameFromClass(string $className): ?string {
        // Check static cache
        if (isset(static::$classTables[$className])) {
            return static::$classTables[$className];
        }

        // Try to get table name from reflection
        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException) { // @phpstan-ignore-line
            // Class not found
            return null;
        }

        // Check if the class is instance of Model
        while ($parent = $reflection->getParentClass()) {
            if ($parent->getName() === Model::class) {
                // Cache result
                static::$classTables[$className] = $className::TABLE;
                return $className::TABLE;
            }
            $reflection = $parent;
        }

        // Class is not a Model
        return null;
    }
}
