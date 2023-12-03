<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\EventListener\ORM;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nucleos\Doctrine\EventListener\ORM\DeletableListener;
use Nucleos\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class DeletableListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new DeletableListener();

        self::assertSame([
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }

    public function testLoadClassMetadataWithEmptyClass(): void
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn(null)
        ;
        $metadata->expects(self::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new DeletableListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithInvalidClass(): void
    {
        $reflection = new ReflectionClass(EmptyClass::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->expects(self::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new DeletableListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithValidClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->method('hasField')->with('deletedAt')
            ->willReturn(false)
        ;
        $metadata->expects(self::once())->method('mapField')->with([
            'type'      => 'datetime',
            'fieldName' => 'deletedAt',
            'nullable'  => true,
        ]);

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new DeletableListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithExistingProperty(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->method('hasField')->with('deletedAt')
            ->willReturn(true)
        ;
        $metadata->expects(self::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new DeletableListener();
        $listener->loadClassMetadata($eventArgs);
    }
}
