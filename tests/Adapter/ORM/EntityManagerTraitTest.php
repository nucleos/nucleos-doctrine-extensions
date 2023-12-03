<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Adapter\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Nucleos\Doctrine\Tests\Fixtures\DemoEntityManager;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\TestCase;

final class EntityManagerTraitTest extends TestCase
{
    public function testCreateQueryBuilder(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('createQueryBuilder')->with('alias', 'someindex')
            ->willReturn($queryBuilder)
        ;

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->method('getRepository')->with(EmptyClass::class)
            ->willReturn($repository)
        ;

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->with(EmptyClass::class)
            ->willReturn($objectManager)
        ;

        $manager = new DemoEntityManager(EmptyClass::class, $registry);

        self::assertSame($queryBuilder, $manager->getQueryBuilder('alias', 'someindex'));
    }
}
