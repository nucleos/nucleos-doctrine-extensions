<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

/**
 * @psalm-type ForeignKey = array{
 *     table:       string,
 *     key:         string,
 *     tmpKey:      string,
 *     nullable:    bool,
 *     name:        string,
 *     primaryKey:  Column[],
 *     onDelete?:   string
 * }
 */
final class IdToUuidMigration implements LoggerAwareInterface
{
    public const UUID_FIELD = 'uuid';

    public const UUID_TYPE = Types::STRING;

    public const UUID_LENGTH = 36;

    /**
     * @var array<int|string, string>
     */
    private array $idToUuidMap = [];

    /**
     * @var array<array-key, array<string, mixed>>
     *
     * @psalm-var array<array-key, ForeignKey>
     */
    private array $foreignKeys = [];

    private string $idField;

    private string $table;

    private Connection $connection;

    private AbstractSchemaManager $schemaManager;

    private LoggerInterface $logger;

    public function __construct(Connection $connection, ?LoggerInterface $logger = null)
    {
        $this->connection    = $connection;
        $this->schemaManager = method_exists($this->connection, 'createSchemaManager')
                             ? $this->connection->createSchemaManager()
                             : $this->connection->getSchemaManager();
        $this->logger        = $logger ?? new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param null|callable(mixed $id, string $uuid): void $callback
     */
    public function migrate(string $tableName, string $idField = 'id', callable $callback = null): void
    {
        $this->section(sprintf('Migrating %s.%s field to UUID', $tableName, $idField));

        $this->prepare($tableName, $idField);
        $this->addUuidFields();
        $this->generateUuidsToReplaceIds();
        $this->addThoseUuidsToTablesWithFK();
        $this->handleCallback($callback);
        $this->deletePreviousFKs();
        $this->deletePrimaryKeys();
        $this->recreateIdFields();
        $this->syncIdFields();
        $this->dropTemporyForeignKeyUuidFields();
        $this->dropIdPrimaryKeyAndSetUuidToPrimaryKey();
        $this->restoreConstraintsAndIndexes();
    }

    private function prepare(string $tableName, string $idField): void
    {
        $this->table       = $tableName;
        $this->idField     = $idField;

        $this->foreignKeys = [];
        $this->idToUuidMap = [];

        foreach ($this->schemaManager->listTables() as $table) {
            $foreignKeys = $this->schemaManager->listTableForeignKeys($table->getName());

            foreach ($foreignKeys as $foreignKey) {
                $key = $foreignKey->getLocalColumns()[0];

                if ($foreignKey->getForeignTableName() !== $this->table) {
                    continue;
                }

                $meta = [
                    'table'      => $table->getName(),
                    'key'        => $key,
                    'tmpKey'     => $key.'_to_uuid',
                    'nullable'   => $this->isForeignKeyNullable($table, $key),
                    'name'       => $foreignKey->getName(),
                    'primaryKey' => $table->getPrimaryKeyColumns(),
                ];

                $onDelete = $foreignKey->onDelete();
                if (null !== $onDelete) {
                    $meta['onDelete'] = $onDelete;
                }

                $this->foreignKeys[] = $meta;
            }
        }

        if (\count($this->foreignKeys) > 0) {
            $this->section('Detected foreign keys:');

            foreach ($this->foreignKeys as $meta) {
                $this->section('  * '.$meta['table'].'.'.$meta['key']);
            }

            return;
        }

        $this->info('No foreign keys detected');
    }

    private function addUuidFields(): void
    {
        $this->section('Adding new "uuid" fields');

        $schema = $this->schemaManager->createSchema();

        $table = $schema->getTable($this->table);
        $table->addColumn(self::UUID_FIELD, self::UUID_TYPE, [
            'length'              => self::UUID_LENGTH,
            'notnull'             => false,
        ]);

        foreach ($this->foreignKeys as $foreignKey) {
            $table = $schema->getTable($foreignKey['table']);
            $table->addColumn($foreignKey['tmpKey'], self::UUID_TYPE, [
                'length'              => self::UUID_LENGTH,
                'notnull'             => false,
                'customSchemaOptions' => ['FIRST'],
            ]);
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function generateUuidsToReplaceIds(): void
    {
        $fetchs = $this->connection->fetchAllAssociative(sprintf('SELECT %s from %s', $this->idField, $this->table));

        if (0 === \count($fetchs)) {
            return;
        }

        $this->section('Generating '.\count($fetchs).' UUIDs');

        foreach ($fetchs as $fetch) {
            $id                     = $fetch[$this->idField];
            $uuid                   = Uuid::v4()->toRfc4122();
            $this->idToUuidMap[$id] = $uuid;
            $this->connection->update($this->table, [
                self::UUID_FIELD => $uuid,
            ], [
                $this->idField => $id,
            ]);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function addThoseUuidsToTablesWithFK(): void
    {
        if (0 === \count($this->foreignKeys)) {
            return;
        }

        $this->section('Adding UUIDs to tables with foreign keys');

        foreach ($this->foreignKeys as $foreignKey) {
            $primaryKeys = array_map(static fn (Column $column) => $column->getName(), $foreignKey['primaryKey']);

            $selectPk = implode(',', $primaryKeys);

            $fetchs   = $this->connection->fetchAllAssociative('SELECT '.$selectPk.', '.$foreignKey['key'].' FROM '.$foreignKey['table']);

            if (0 === \count($fetchs)) {
                continue;
            }

            $this->debug('Adding '.\count($fetchs).' UUIDs to "'.$foreignKey['table'].'.'.$foreignKey['key']);

            foreach ($fetchs as $fetch) {
                if (null === $fetch[$foreignKey['key']]) {
                    continue;
                }

                $queryPk = array_flip($primaryKeys);
                foreach ($queryPk as $key => $value) {
                    $queryPk[$key] = $fetch[$key];
                }

                $this->connection->update($foreignKey['table'], [
                    $foreignKey['tmpKey'] => $this->idToUuidMap[$fetch[$foreignKey['key']]],
                ], $queryPk);
            }
        }
    }

    /**
     * @param null|callable(mixed $id, string $uuid): void $callback
     */
    private function handleCallback(?callable $callback): void
    {
        if (null === $callback) {
            return;
        }

        $this->section('Executing callback');

        foreach ($this->idToUuidMap as $old => $new) {
            $callback($old, $new);
        }
    }

    private function deletePreviousFKs(): void
    {
        $this->section('Deleting previous foreign keys');

        $schema = $this->schemaManager->createSchema();

        foreach ($this->foreignKeys as $foreignKey) {
            $table = $schema->getTable($foreignKey['table']);

            $table->removeForeignKey($foreignKey['name']);
            $table->dropColumn($foreignKey['key']);
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function deletePrimaryKeys(): void
    {
        $this->section('Deleting previous primary keys');

        $schema = $this->schemaManager->createSchema();

        foreach ($this->foreignKeys as $foreignKey) {
            $table = $schema->getTable($foreignKey['table']);

            if ([] !== $foreignKey['primaryKey']) {
                if ($table->hasPrimaryKey()) {
                    $table->dropPrimaryKey();
                }
            }
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function recreateIdFields(): void
    {
        $this->section('Recreate id fields');

        $schema = $this->schemaManager->createSchema();

        $table  = $schema->getTable($this->table);
        $table->dropColumn($this->idField);
        $table->addColumn($this->idField, self::UUID_TYPE, [
            'length'              => self::UUID_LENGTH,
            'notnull'             => false,
            'customSchemaOptions' => ['FIRST'],
        ]);

        foreach ($this->foreignKeys as $foreignKey) {
            $table  = $schema->getTable($foreignKey['table']);

            $table->dropColumn($foreignKey['key']);
            $table->addColumn($foreignKey['key'], self::UUID_TYPE, [
                'length'  => self::UUID_LENGTH,
                'notnull' => false,
            ]);
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function syncIdFields(): void
    {
        $this->section('Copy UUIDs to recreated ids fields');

        $this->connection->executeQuery(sprintf('UPDATE %s SET %s = %s', $this->table, $this->idField, self::UUID_FIELD));

        foreach ($this->foreignKeys as $foreignKey) {
            $this->connection->executeQuery(sprintf('UPDATE %s SET %s = %s', $foreignKey['table'], $foreignKey['key'], $foreignKey['tmpKey']));
        }
    }

    private function dropTemporyForeignKeyUuidFields(): void
    {
        $this->section('Drop temporary foreign key uuid fields');

        $schema = $this->schemaManager->createSchema();

        foreach ($this->foreignKeys as $foreignKey) {
            $table  = $schema->getTable($foreignKey['table']);

            $table->dropColumn($foreignKey['tmpKey']);
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function dropIdPrimaryKeyAndSetUuidToPrimaryKey(): void
    {
        $this->section('Creating the new primary key');

        $schema = $this->schemaManager->createSchema();
        $table  = $schema->getTable($this->table);

        $table->dropPrimaryKey();
        $table->dropColumn(self::UUID_FIELD);

        $table->setPrimaryKey([$this->idField]);

        $this->schemaManager->migrateSchema($schema);
    }

    private function restoreConstraintsAndIndexes(): void
    {
        $schema = $this->schemaManager->createSchema();

        foreach ($this->foreignKeys as $foreignKey) {
            $table = $schema->getTable($foreignKey['table']);

            if ([] !== $foreignKey['primaryKey']) {
                $primaryKeys = array_map(static fn (Column $column) => $column->getName(), $foreignKey['primaryKey']);

                if (!$table->hasPrimaryKey()) {
                    $table->setPrimaryKey($primaryKeys);
                }
            }

            $table->addForeignKeyConstraint($this->table, [$foreignKey['key']], [$this->idField], [
                'onDelete' => $foreignKey['onDelete'] ?? null,
            ]);
            $table->addIndex([$foreignKey['key']]);
        }

        $this->schemaManager->migrateSchema($schema);
    }

    private function section(string $message): void
    {
        $this->writeLn(sprintf('%s', $message));
    }

    private function info(string $message): void
    {
        $this->writeLn(sprintf('-> %s', $message));
    }

    private function debug(string $message): void
    {
        $this->writeLn(sprintf('  * %s', $message));
    }

    private function writeLn(string $message): void
    {
        $this->logger->notice($message, [
            'migration' => $this,
        ]);
    }

    private function isForeignKeyNullable(Table $table, string $key): bool
    {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === $key) {
                return !$column->getNotnull();
            }
        }

        throw new RuntimeException('Unable to find '.$key.'in '.$table->getName());
    }
}
