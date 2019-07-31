<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\EventListener\ORM;

use Core23\Doctrine\Model\Traits\ConfirmableTrait;
use Core23\Doctrine\Util\ClassUtils;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\MappingException;

final class ConfirmableListener extends AbstractListener
{
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();

        $reflClass = $meta->getReflectionClass();

        if (null === $reflClass || !ClassUtils::containsTrait($reflClass, ConfirmableTrait::class)) {
            return;
        }

        $this->createDateTimeField($meta, 'confirmedAt', true);
    }
}
