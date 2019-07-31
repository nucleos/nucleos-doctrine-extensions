<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\EventListener\ORM;

use Core23\Doctrine\EventListener\ORM\ConfirmableListener;
use Core23\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Core23\Doctrine\Tests\Fixtures\EmptyClass;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ReflectionClass;

final class ConfirmableListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new ConfirmableListener();

        static::assertSame([
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
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

        $listener = new ConfirmableListener();
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

        $listener = new ConfirmableListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithValidClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('confirmedAt')
            ->willReturn(false)
        ;
        $metadata->mapField([
            'type'      => 'datetime',
            'fieldName' => 'confirmedAt',
            'nullable'  => true,
        ])
            ->shouldBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new ConfirmableListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }

    public function testLoadClassMetadataWithExistingProperty(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()
            ->willReturn($reflection)
        ;
        $metadata->hasField('confirmedAt')
            ->willReturn(true)
        ;
        $metadata->mapField(Argument::any())
            ->shouldNotBeCalled()
        ;

        $eventArgs = $this->prophesize(LoadClassMetadataEventArgs::class);
        $eventArgs->getClassMetadata()
            ->willReturn($metadata)
        ;

        $listener = new ConfirmableListener();
        $listener->loadClassMetadata($eventArgs->reveal());
    }
}
