<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\EventListener\ORM;

use Core23\Doctrine\Model\PositionAwareInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class SortableListener implements EventSubscriber
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

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::loadClassMetadata,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        if (!$args->getEntity() instanceof PositionAwareInterface) {
            return;
        }

        $this->uniquePosition($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if (!$args->getEntity() instanceof PositionAwareInterface) {
            return;
        }

        $position = $args->getEntity()->getPosition();

        if ($args->hasChangedField('position')) {
            $position = $args->getOldValue('position');
        }

        $this->uniquePosition($args, $position);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof PositionAwareInterface) {
            $this->movePosition($args->getEntityManager(), $entity, -1);
        }
    }

    /**
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();

        $reflClass = $meta->getReflectionClass();

        if (null === $reflClass || !$reflClass->implementsInterface(PositionAwareInterface::class)) {
            return;
        }

        if (!$meta->hasField('position')) {
            $meta->mapField(
                [
                    'type'      => 'integer',
                    'fieldName' => 'position',
                ]
            );
        }
    }

    private function uniquePosition(LifecycleEventArgs $args, ?int $oldPosition = null): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof PositionAwareInterface) {
            if (null === $entity->getPosition()) {
                $position = $this->getNextPosition($args->getEntityManager(), $entity);
                $entity->setPosition($position);
            } elseif (null !== $oldPosition && $oldPosition !== $entity->getPosition()) {
                $this->movePosition($args->getEntityManager(), $entity);
            }
        }
    }

    private function movePosition(EntityManager $em, PositionAwareInterface $entity, int $direction = 1): void
    {
        $uow  = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(\get_class($entity));

        $qb = $em->createQueryBuilder()
            ->update($meta->getName(), 'e')
            ->set('e.position', 'e.position + '.$direction)
        ;

        if ($direction > 0) {
            $qb->andWhere('e.position <= :position')->setParameter('position', $entity->getPosition());
        } elseif ($direction < 0) {
            $qb->andWhere('e.position >= :position')->setParameter('position', $entity->getPosition());
        } else {
            return;
        }

        $this->addGroupFilter($qb, $entity, $uow);

        $qb->getQuery()->execute();
    }

    private function getNextPosition(EntityManager $em, PositionAwareInterface $entity): int
    {
        $meta = $em->getClassMetadata(\get_class($entity));

        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from($meta->getName(), 'e')
            ->addOrderBy('e.position', 'DESC')
            ->setMaxResults(1)
        ;

        $this->addGroupFilter($qb, $entity);

        try {
            $result = $qb->getQuery()->getOneOrNullResult();

            if ($result instanceof PositionAwareInterface && null !== $result->getPosition()) {
                return $result->getPosition() + 1;
            }
        } catch (NonUniqueResultException $ignored) {
        }

        return 0;
    }

    private function addGroupFilter(QueryBuilder $qb, PositionAwareInterface $entity, UnitOfWork $uow = null): void
    {
        foreach ($entity->getPositionGroup() as $field) {
            $value = $this->propertyAccessor->getValue($entity, $field);

            if (\is_object($value) && (null === $uow || null === $uow->getSingleIdentifierValue($value))) {
                continue;
            }

            $qb->andWhere('e.'.$field.' = :'.$field)->setParameter($field, $value);
        }
    }
}
