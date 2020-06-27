<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\EventListener\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nucleos\Doctrine\EventListener\ORM\UniqueActiveListener;
use Nucleos\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;
use stdClass;

final class UniqueActiveListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new UniqueActiveListener();

        static::assertSame([
            Events::prePersist,
            Events::preUpdate,
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }

    public function testPrePersistForInvalidClass(): void
    {
        $object = $this->prophesize(stdClass::class);

        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getEntity()
            ->willReturn($object)
        ;
        $eventArgs->getEntityManager()
            ->willReturn($entityManager)
        ;

        $listener = new UniqueActiveListener();
        $listener->prePersist($eventArgs->reveal());

        $entityManager->createQueryBuilder()->shouldNotHaveBeenCalled();
    }

    public function testPreUpdateForInvalidClass(): void
    {
        $object = $this->prophesize(stdClass::class);

        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getEntity()
            ->willReturn($object)
        ;
        $eventArgs->getEntityManager()
            ->willReturn($entityManager)
        ;

        $listener = new UniqueActiveListener();
        $listener->preUpdate($eventArgs->reveal());

        $entityManager->createQueryBuilder()->shouldNotHaveBeenCalled();
    }

    public function testLoadClassMetadataWithEmptyClass(): void
    {
        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn(null)
        ;
        $metadata->mapField(Argument::any())
            ->shouldNotBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new UniqueActiveListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithInvalidClass(): void
    {
        $reflection = new ReflectionClass(EmptyClass::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->mapField(Argument::any())
            ->shouldNotBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new UniqueActiveListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithValidClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('active')
            ->willReturn(false)
        ;
        $metadata->mapField([
            'type'      => 'integer',
            'fieldName' => 'active',
        ])
            ->shouldBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new UniqueActiveListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithExistingProperty(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('active')
            ->willReturn(true)
        ;
        $metadata->mapField(Argument::any())
            ->shouldNotBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new UniqueActiveListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }
}
