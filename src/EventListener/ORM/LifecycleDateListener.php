<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\EventListener\ORM;

use DateTimeImmutable;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Nucleos\Doctrine\Model\LifecycleAware;

final class LifecycleDateListener extends AbstractListener
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::loadClassMetadata,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleAware) {
            $object->setCreatedAt(new DateTimeImmutable());
            $object->setUpdatedAt(new DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleAware) {
            $object->setUpdatedAt(new DateTimeImmutable());
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();

        $reflClass = $meta->getReflectionClass();

        if (null === $reflClass || !$reflClass->implementsInterface(LifecycleAware::class)) {
            return;
        }

        $this->createDateTimeField($meta, 'createdAt', false);
        $this->createDateTimeField($meta, 'updatedAt', false);
    }
}
