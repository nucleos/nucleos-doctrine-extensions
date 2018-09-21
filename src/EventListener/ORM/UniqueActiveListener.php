<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\EventListener\ORM;

use Core23\DoctrineExtensions\Model\UniqueActiveInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class UniqueActiveListener implements EventSubscriber
{
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

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->uniqueActive($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->uniqueActive($args);
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

        if ( !$reflClass->implementsInterface(UniqueActiveInterface::class)) {
            return;
        }

        if (!$meta->hasField('active')) {
            $meta->mapField([
                'type'      => 'integer',
                'fieldName' => 'active',
            ]);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function uniqueActive(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof UniqueActiveInterface && $entity->isActive()) {
            $em   = $args->getEntityManager();
            $uow  = $em->getUnitOfWork();
            $meta = $em->getClassMetadata(\get_class($entity));

            $qb = $em->createQueryBuilder()
                ->update($meta->getName(), 'e')
                ->set('e.active', 'false')
                ->andWhere('e.active = true');

            if ($entity->getId()) {
                $qb->andWhere('e.id != :id')->setParameter('id', $entity->getId());
            }

            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($entity->getUniqueActiveFields() as $field) {
                $value = $propertyAccessor->getValue($entity, $field);

                if (\is_object($value) && null === $uow->getSingleIdentifierValue($value)) {
                    continue;
                }

                $qb->andWhere('e.'.$field.' = :'.$field)->setParameter($field, $value);
            }

            $qb->getQuery()->execute();
        }
    }
}
