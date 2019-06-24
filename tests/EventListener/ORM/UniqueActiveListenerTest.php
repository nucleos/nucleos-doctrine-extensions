<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\EventListener\ORM;

use Core23\Doctrine\EventListener\ORM\UniqueActiveListener;
use Core23\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Core23\Doctrine\Tests\Fixtures\EmptyClass;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use LogicException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;
use stdClass;

final class UniqueActiveListenerTest extends TestCase
{
    public function testItIsInstantiable(): void
    {
        $listener = new UniqueActiveListener();

        static::assertInstanceOf(EventSubscriber::class, $listener);
    }

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

        $eventArgs = $this->prophesize(PreUpdateEventArgs::class);
        $eventArgs->getEntity()
            ->willReturn($object)
        ;

        $listener = new UniqueActiveListener();
        $listener->prePersist($eventArgs->reveal());

        static::assertTrue(true);
    }

    public function testPreUpdateForInvalidClass(): void
    {
        $object = $this->prophesize(stdClass::class);

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getEntity()
            ->willReturn($object)
        ;

        $listener = new UniqueActiveListener();
        $listener->preUpdate($eventArgs->reveal());

        static::assertTrue(true);
    }

    public function testLoadClassMetadataWithNoValidData(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Class metadata was no ORM');

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn(null)
        ;

        $listener = new UniqueActiveListener();
        $listener->loadClassMetadata($eventArgs->reveal());
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
