<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\EventListener\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Id\SequenceGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class TablePrefixEventListener implements EventSubscriber
{
    /**
     * @var string|null
     */
    private $prefix;

    public function __construct(?string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        if (null === $this->prefix) {
            return;
        }

        $classMetadata = $args->getClassMetadata();
        $entityManager = $args->getEntityManager();

        $this->addTablePrefix($classMetadata);
        $this->addSequencePrefix($classMetadata, $entityManager);
    }

    private function addTablePrefix(ClassMetadata $classMetadata): void
    {
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            return;
        }

        $tableName = $classMetadata->getTableName();

        if (!$this->prefixExists($tableName)) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix.$tableName,
            ]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            $this->evaluteMapping($classMetadata, $mapping, $fieldName);
        }
    }

    private function addSequencePrefix(ClassMetadata $classMetadata, EntityManager $em): void
    {
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            return;
        }

        if (!$classMetadata->isIdGeneratorSequence()) {
            return;
        }

        $newDefinition  = $classMetadata->sequenceGeneratorDefinition;

        $sequenceName = $newDefinition['sequenceName'];
        if (!$this->prefixExists($sequenceName)) {
            $newDefinition['sequenceName'] = $this->prefix.$sequenceName;
        }

        $classMetadata->setSequenceGeneratorDefinition($newDefinition);

        if (isset($classMetadata->idGenerator)) {
            $this->addSequenceGenerator($classMetadata, $em, $newDefinition);
        }
    }

    private function prefixExists(string $name): bool
    {
        return 0 === strpos($name, (string) $this->prefix);
    }

    private function evaluteMapping(ClassMetadata $classMetadata, array $mapping, string $fieldName): void
    {
        if (ClassMetadataInfo::MANY_TO_MANY !== $mapping['type']) {
            return;
        }

        if (isset($classMetadata->associationMappings[$fieldName]['joinTable']['name'])) {
            $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];

            if (!$this->prefixExists($mappedTableName)) {
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix.$mappedTableName;
            }
        }
    }

    private function addSequenceGenerator(ClassMetadata $classMetadata, EntityManager $em, array $definition): void
    {
        $sequenceGenerator = new SequenceGenerator(
            $em->getConfiguration()->getQuoteStrategy()->getSequenceName(
                $definition,
                $classMetadata,
                $em->getConnection()->getDatabasePlatform()
            ),
            $definition['allocationSize']
        );
        $classMetadata->setIdGenerator($sequenceGenerator);
    }
}
