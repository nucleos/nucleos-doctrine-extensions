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
use Exception;
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
    /**
     * @var array<int, string>
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

    public function __construct(Connection $connection, ?LoggerInterface $logger)
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

    public function migrate(string $tableName, string $idField = 'id'): void
    {
        $this->writeln(sprintf('Migrating %s.%s field to UUID...', $tableName, $idField));
        $this->prepare($tableName, $idField);
        $this->addUuidFields();
        $this->generateUuidsToReplaceIds();
        $this->addThoseUuidsToTablesWithFK();
        $this->deletePreviousFKs();
        $this->renameNewFKsToPreviousNames();
        $this->dropIdPrimaryKeyAndSetUuidToPrimaryKey();
        $this->restoreConstraintsAndIndexes();
        $this->writeln(sprintf('Successfully migrated %s.%s to UUID', $tableName, $idField));
    }

    private function writeln(string $message): void
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
            $this->writeln('-> Detected foreign keys:');

            foreach ($this->foreignKeys as $meta) {
                $this->writeln('  * '.$meta['table'].'.'.$meta['key']);
            }

            return;
        }

        $this->writeln('-> No foreign keys detected.');
    }

    private function addUuidFields(): void
    {
        $this->connection->executeQuery('ALTER TABLE '.$this->table.' ADD uuid VARCHAR(36) FIRST');

        foreach ($this->foreignKeys as $foreignKey) {
            $this->connection->executeQuery('ALTER TABLE '.$foreignKey['table'].' ADD '.$foreignKey['tmpKey'].' VARCHAR(36)');
        }
    }

    private function generateUuidsToReplaceIds(): void
    {
        $fetchs = $this->connection->fetchAllAssociative(sprintf('SELECT %s from %s', $this->idField, $this->table));

        if (0 === \count($fetchs)) {
            return;
        }

        $this->writeln('-> Generating '.\count($fetchs).' UUID(s)...');

        foreach ($fetchs as $fetch) {
            $id                     = $fetch[$this->idField];
            $uuid                   = Uuid::v4()->toRfc4122();
            $this->idToUuidMap[$id] = $uuid;
            $this->connection->update($this->table, [
                'uuid' => $uuid,
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

        $this->writeln('-> Adding UUIDs to tables with foreign keys...');

        foreach ($this->foreignKeys as $foreignKey) {
            $primaryKeys = array_map(static fn (Column $column) => $column->getName(), $foreignKey['primaryKey']);

            $selectPk = implode(',', $primaryKeys);

            $fetchs   = $this->connection->fetchAllAssociative('SELECT '.$selectPk.', '.$foreignKey['key'].' FROM '.$foreignKey['table']);

            if (0 === \count($fetchs)) {
                continue;
            }

            $this->writeln('  * Adding '.\count($fetchs).' UUIDs to "'.$foreignKey['table'].'.'.$foreignKey['key'].'"...');

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

    private function deletePreviousFKs(): void
    {
        $this->writeln('-> Deleting previous foreign keys...');

        foreach ($this->foreignKeys as $foreignKey) {
            if ([] !== $foreignKey['primaryKey']) {
                try {
                    // drop primary key if not already dropped
                    $this->connection->executeQuery('ALTER TABLE '.$foreignKey['table'].' DROP PRIMARY KEY');
                } catch (Exception) {
                }
            }

            $this->connection->executeQuery('ALTER TABLE '.$foreignKey['table'].' DROP FOREIGN KEY '.$foreignKey['name']);
            $this->connection->executeQuery('ALTER TABLE '.$foreignKey['table'].' DROP COLUMN '.$foreignKey['key']);
        }
    }

    private function renameNewFKsToPreviousNames(): void
    {
        $this->writeln('-> Renaming temporary foreign keys to previous foreign keys names...');

        foreach ($this->foreignKeys as $fk) {
            $this->connection->executeQuery('ALTER TABLE '.$fk['table'].' CHANGE '.$fk['tmpKey'].' '.$fk['key'].' VARCHAR(36) '.(true === $fk['nullable'] ? '' : 'NOT NULL '));
        }
    }

    private function dropIdPrimaryKeyAndSetUuidToPrimaryKey(): void
    {
        $this->writeln('-> Creating the new primary key...');

        $this->connection->executeQuery('ALTER TABLE '.$this->table.' DROP PRIMARY KEY, DROP COLUMN '.$this->idField);
        $this->connection->executeQuery('ALTER TABLE '.$this->table.' CHANGE uuid '.$this->idField.' VARCHAR(36) NOT NULL');
        $this->connection->executeQuery('ALTER TABLE '.$this->table.' ADD PRIMARY KEY ('.$this->idField.')');
    }

    private function restoreConstraintsAndIndexes(): void
    {
        foreach ($this->foreignKeys as $foreignKey) {
            if ([] !== $foreignKey['primaryKey']) {
                $primaryKeys = array_map(static fn (Column $column) => $column->getName(), $foreignKey['primaryKey']);

                try {
                    // restore primary key if not already restored
                    $this->connection->executeQuery('ALTER TABLE '.$foreignKey['table'].' ADD PRIMARY KEY ('.implode(',', $primaryKeys).')');
                } catch (Exception) {
                }
            }

            $this->connection->executeQuery(
                'ALTER TABLE '.$foreignKey['table'].' ADD CONSTRAINT '.$foreignKey['name'].' FOREIGN KEY ('.$foreignKey['key'].') REFERENCES '.$this->table.' ('.$this->idField.')'.
              (isset($foreignKey['onDelete']) ? ' ON DELETE '.$foreignKey['onDelete'] : '')
            );

            $this->connection->executeQuery('CREATE INDEX '.str_replace('FK_', 'IDX_', $foreignKey['name']).' ON '.$foreignKey['table'].' ('.$foreignKey['key'].')');
        }
    }
}
