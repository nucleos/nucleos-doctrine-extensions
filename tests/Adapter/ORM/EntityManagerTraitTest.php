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
use Prophecy\PhpUnit\ProphecyTrait;

final class EntityManagerTraitTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateQueryBuilder(): void
    {
        $queryBuilder = $this->prophesize(QueryBuilder::class);

        $repository = $this->prophesize(EntityRepository::class);
        $repository->createQueryBuilder('alias', 'someindex')
            ->willReturn($queryBuilder)
        ;

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository(EmptyClass::class)
            ->willReturn($repository)
        ;

        $registry = $this->prophesize(ManagerRegistry::class);
        $registry->getManagerForClass(EmptyClass::class)
            ->willReturn($objectManager)
        ;

        $manager = new DemoEntityManager(EmptyClass::class, $registry->reveal());

        static::assertSame($queryBuilder->reveal(), $manager->getQueryBuilder('alias', 'someindex'));
    }
}
