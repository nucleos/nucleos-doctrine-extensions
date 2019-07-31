<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\EventListener\ORM;

use Core23\Doctrine\EventListener\ORM\LifecycleDateListener;
use Core23\Doctrine\Model\LifecycleDateTimeInterface;
use Core23\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Core23\Doctrine\Tests\Fixtures\EmptyClass;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;
use stdClass;

final class LifecycleDateListenerTest extends TestCase
{
    public function testItIsInstantiable(): void
    {
        $listener = new LifecycleDateListener();

        static::assertInstanceOf(EventSubscriber::class, $listener);
    }

    public function testGetSubscribedEvents(): void
    {
        $listener = new LifecycleDateListener();

        static::assertSame([
            Events::prePersist,
            Events::preUpdate,
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }

    public function testPrePersist(): void
    {
        $object = $this->prophesize(LifecycleDateTimeInterface::class);
        $object->setCreatedAt(Argument::type(DateTime::class))
            ->shouldBeCalled()
        ;
        $object->setUpdatedAt(Argument::type(DateTime::class))
            ->shouldBeCalled()
        ;

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getObject()
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->prePersist($eventArgs->reveal());
    }

    public function testPrePersistForInvalidClass(): void
    {
        $object = $this->prophesize(stdClass::class);

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getObject()
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->prePersist($eventArgs->reveal());

        static::assertTrue(true);
    }

    public function testPreUpdate(): void
    {
        $object = $this->prophesize(LifecycleDateTimeInterface::class);
        $object->setUpdatedAt(Argument::type(DateTime::class))
            ->shouldBeCalled()
        ;

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getObject()
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->preUpdate($eventArgs->reveal());
    }

    public function testPreUpdateForInvalidClass(): void
    {
        $object = $this->prophesize(stdClass::class);

        $eventArgs = $this->prophesize(LifecycleEventArgs::class);
        $eventArgs->getObject()
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->preUpdate($eventArgs->reveal());

        static::assertTrue(true);
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

        $listener = new LifecycleDateListener();
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

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithValidClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('createdAt')
            ->willReturn(false)
        ;
        $metadata->hasField('updatedAt')
            ->willReturn(false)
        ;
        $metadata->mapField([
            'type'      => 'datetime',
            'fieldName' => 'createdAt',
            'nullable'  => false,
        ])
            ->shouldBeCalled()
        ;
        $metadata->mapField([
            'type'      => 'datetime',
            'fieldName' => 'updatedAt',
            'nullable'  => false,
        ])
            ->shouldBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithExistingProperty(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('createdAt')
            ->willReturn(true)
        ;
        $metadata->hasField('updatedAt')
            ->willReturn(true)
        ;
        $metadata->mapField(Argument::any())
            ->shouldNotBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }
}
