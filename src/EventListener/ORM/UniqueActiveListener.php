<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\EventListener\ORM;

use Core23\Doctrine\Model\UniqueActiveInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use LogicException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class UniqueActiveListener implements EventSubscriber
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor = null)
    {
        if (null === $propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::loadClassMetadata,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->uniqueActive($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->uniqueActive($args);
    }

    /**
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();

        if (!$meta instanceof ClassMetadata) {
            throw new LogicException('Class metadata was no ORM');
        }

        $reflClass = $meta->getReflectionClass();

        if (null === $reflClass || !$reflClass->implementsInterface(UniqueActiveInterface::class)) {
            return;
        }

        if (!$meta->hasField('active')) {
            $meta->mapField([
                'type'      => 'integer',
                'fieldName' => 'active',
            ]);
        }
    }

    private function uniqueActive(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UniqueActiveInterface) {
            return;
        }

        if (!$entity->isActive()) {
            return;
        }

        $em   = $args->getEntityManager();
        $uow  = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(\get_class($entity));

        $qb = $em->createQueryBuilder()
            ->update($meta->getName(), 'e')
            ->set('e.active', 'false')
            ->andWhere('e.active = true')
        ;

        foreach ($meta->getIdentifier() as $key) {
            $qb->andWhere(sprintf('e.%s != :%s', $key, $key))
                ->setParameter($key, $this->propertyAccessor->getValue($entity, $key))
            ;
        }

        $this->addFieldFilter($qb, $entity, $uow);

        $qb->getQuery()->execute();
    }

    private function addFieldFilter(QueryBuilder $qb, UniqueActiveInterface $entity, UnitOfWork $uow): void
    {
        foreach ($entity->getUniqueActiveFields() as $field) {
            $value = $this->propertyAccessor->getValue($entity, $field);

            if (\is_object($value) && null === $uow->getSingleIdentifierValue($value)) {
                continue;
            }

            $qb->andWhere('e.'.$field.' = :'.$field)->setParameter($field, $value);
        }
    }
}
