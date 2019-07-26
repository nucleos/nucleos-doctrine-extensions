<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\EventListener\ORM;

use Core23\Doctrine\Model\LifecycleDateTimeInterface;
use Core23\Doctrine\Model\Traits\LifecycleDateTimeTrait;
use Core23\Doctrine\Util\ClassUtils;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use LogicException;

final class LifecycleDateListener extends AbstractListener
{
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
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleDateTimeInterface) {
            $object->setCreatedAt(new DateTime());
            $object->setUpdatedAt(new DateTime());
        }
    }

    /**
     * Update LifecycleDateTime.
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof LifecycleDateTimeInterface) {
            $object->setUpdatedAt(new DateTime());
        }
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

        if (null === $reflClass || !ClassUtils::containsTrait($reflClass, LifecycleDateTimeTrait::class)) {
            return;
        }

        $this->createDateTimeField($meta, 'createdAt', false);
        $this->createDateTimeField($meta, 'updatedAt', false);
    }
}
