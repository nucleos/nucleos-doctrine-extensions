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
use Core23\Doctrine\Model\Traits\SortableTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class SortableListener extends AbstractListener
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        if ($args->getEntity() instanceof PositionAwareInterface) {
            $this->uniquePosition($args);
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if ($args->getEntity() instanceof PositionAwareInterface) {
            $position = $args->getEntity()->getPosition();

            if ($args->hasChangedField('position')) {
                $position = $args->getOldValue('position');
            }

            $this->uniquePosition($args, $position);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof PositionAwareInterface) {
            $this->movePosition($args->getEntityManager(), $entity, -1);
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     *
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();

        if (!$meta instanceof ClassMetadata) {
            throw new \LogicException(sprintf('Class metadata was no ORM but %s', \get_class($meta)));
        }

        $reflClass = $meta->getReflectionClass();

        if (null === $reflClass || !$this->containsTrait($reflClass, SortableTrait::class)) {
            return;
        }

        if (!$meta->hasField('position')) {
            $meta->mapField([
                'type'      => 'integer',
                'fieldName' => 'position',
            ]);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param int|null           $oldPosition
     */
    private function uniquePosition(LifecycleEventArgs $args, ?int $oldPosition = null): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof PositionAwareInterface) {
            if (null === $entity->getPosition()) {
                $position = $this->getNextPosition($args->getEntityManager(), $entity);
                $entity->setPosition($position);
            } elseif ($oldPosition && $oldPosition !== $entity->getPosition()) {
                $this->movePosition($args->getEntityManager(), $entity);
            }
        }
    }

    /**
     * @param EntityManager          $em
     * @param PositionAwareInterface $entity
     * @param int                    $direction
     */
    private function movePosition(EntityManager $em, PositionAwareInterface $entity, int $direction = 1): void
    {
        $uow  = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(\get_class($entity));

        $qb = $em->createQueryBuilder()
            ->update($meta->getName(), 'e')
            ->set('e.position', 'e.position + '.$direction);

        if ($direction > 0) {
            $qb->andWhere('e.position <= :position')->setParameter('position', $entity->getPosition());
        } elseif ($direction < 0) {
            $qb->andWhere('e.position >= :position')->setParameter('position', $entity->getPosition());
        } else {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($entity->getPositionGroup() as $field) {
            $value = $propertyAccessor->getValue($entity, $field);

            if (\is_object($value) && null === $uow->getSingleIdentifierValue($value)) {
                continue;
            }

            $qb->andWhere('e.'.$field.' = :'.$field)->setParameter($field, $value);
        }

        $qb->getQuery()->execute();
    }

    /**
     * @param EntityManager          $em
     * @param PositionAwareInterface $entity
     *
     * @return int
     */
    private function getNextPosition(EntityManager $em, PositionAwareInterface $entity): int
    {
        $meta = $em->getClassMetadata(\get_class($entity));

        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from($meta->getName(), 'e')
            ->addOrderBy('e.position', 'DESC')
            ->setMaxResults(1);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($entity->getPositionGroup() as $field) {
            $value = $propertyAccessor->getValue($entity, $field);
            $qb->andWhere('e.'.$field.' = :'.$field)->setParameter($field, $value);
        }

        /* @var PositionAwareInterface $result */
        try {
            $result = $qb->getQuery()->getOneOrNullResult();

            return ($result ? $result->getPosition() : 0) + 1;
        } catch (NonUniqueResultException $ignored) {
            return 0;
        }
    }
}
