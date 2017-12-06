<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\EventListener\ORM;

use Core23\DoctrineExtensions\Model\LifecycleDateTimeInterface;
use Core23\DoctrineExtensions\Model\Traits\LifecycleDateTimeTrait;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

final class LifecycleDateListener extends AbstractListener
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
     * Start lifecycle.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleDateTimeInterface) {
            $object->setCreatedAt(new \DateTime());
            $object->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * Update LifecycleDateTime.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleDateTimeInterface) {
            $object->setUpdatedAt(new \DateTime());
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
            throw new \LogicException(sprintf('Class metadata was no ORM but %s', get_class($meta)));
        }

        if (!$this->containsTrait($meta->getReflectionClass(), LifecycleDateTimeTrait::class)) {
            return;
        }

        if (!$meta->hasField('createdAt')) {
            $meta->mapField([
                'type'      => 'datetime',
                'fieldName' => 'createdAt',
            ]);
        }
        if (!$meta->hasField('updatedAt')) {
            $meta->mapField([
                'type'      => 'datetime',
                'fieldName' => 'updatedAt',
            ]);
        }
    }
}
