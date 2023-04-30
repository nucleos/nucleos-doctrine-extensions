<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\EventListener\ORM;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nucleos\Doctrine\EventListener\ORM\LifecycleDateListener;
use Nucleos\Doctrine\Model\LifecycleDateTimeInterface;
use Nucleos\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

final class LifecycleDateListenerTest extends TestCase
{
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
        $object = $this->createMock(LifecycleDateTimeInterface::class);
        $object->expects(static::once())->method('setCreatedAt');
        $object->expects(static::once())->method('setUpdatedAt');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->prePersist($eventArgs);
    }

    public function testPrePersistForInvalidClass(): void
    {
        $object = $this->createMock(stdClass::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($object)
        ;
        $eventArgs->method('getEntityManager')
            ->willReturn($entityManager)
        ;

        $listener = new LifecycleDateListener();
        $listener->prePersist($eventArgs);

        $entityManager->expects(static::never())->method('createQueryBuilder');
    }

    public function testPreUpdate(): void
    {
        $object = $this->createMock(LifecycleDateTimeInterface::class);
        $object->expects(static::once())->method('setUpdatedAt');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($object)
        ;

        $listener = new LifecycleDateListener();
        $listener->preUpdate($eventArgs);
    }

    public function testPreUpdateForInvalidClass(): void
    {
        $object = $this->createMock(stdClass::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($object)
        ;
        $eventArgs->method('getEntityManager')
            ->willReturn($entityManager)
        ;

        $listener = new LifecycleDateListener();
        $listener->preUpdate($eventArgs);

        $entityManager->expects(static::never())->method('createQueryBuilder');
    }

    public function testLoadClassMetadataWithEmptyClass(): void
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn(null)
        ;
        $metadata->expects(static::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithInvalidClass(): void
    {
        $reflection = new ReflectionClass(EmptyClass::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->expects(static::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithValidClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->expects($matcher = static::exactly(2))->method('hasField')
            ->willReturnCallback($this->withParameter($matcher, [
                ['createdAt'],
                ['updatedAt'],
            ]))
            ->willReturn(false)
        ;
        $metadata->expects($matcher = static::exactly(2))->method('mapField')
            ->willReturnCallback($this->withParameter($matcher, [
                [[
                    'type'      => 'datetime',
                    'fieldName' => 'createdAt',
                    'nullable'  => false,
                ]],
                [[
                    'type'      => 'datetime',
                    'fieldName' => 'updatedAt',
                    'nullable'  => false,
                ]],
            ]))
            ->willReturn(false)
        ;

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs);
    }

    public function testLoadClassMetadataWithExistingProperty(): void
    {
        $reflection = new ReflectionClass(ClassWithAllProperties::class);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getReflectionClass')
            ->willReturn($reflection)
        ;
        $metadata->expects($matcher = static::exactly(2))->method('hasField')
            ->willReturnCallback($this->withParameter($matcher, [
                ['createdAt'],
                ['updatedAt'],
            ]))
            ->willReturn(true)
        ;
        $metadata->expects(static::never())->method('mapField');

        $eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')
            ->willReturn($metadata)
        ;

        $listener = new LifecycleDateListener();
        $listener->loadClassMetadata($eventArgs);
    }

    /**
     * @param array<array-key, mixed[]> $parameters
     */
    protected function withParameter(InvokedCount $matcher, array $parameters): Closure
    {
        return static function () use ($matcher, $parameters): void {
            /** @psalm-suppress InternalMethod */
            $callNumber = $matcher->numberOfInvocations();

            Assert::assertEquals($parameters[$callNumber-1], \func_get_args(), sprintf('Call %s', $callNumber));
        };
    }
}
