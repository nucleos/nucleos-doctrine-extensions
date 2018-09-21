<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\EventListener\ORM;

use Core23\DoctrineExtensions\Model\Traits\DeleteableTrait;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

final class DeletableListener extends AbstractListener
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
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

        if ( !$this->containsTrait($reflClass, DeleteableTrait::class)) {
            return;
        }

        if (!$meta->hasField('deletedAt')) {
            $meta->mapField([
                'type'      => 'datetime',
                'fieldName' => 'deletedAt',
                'nullable'  => true,
            ]);
        }
    }
}
