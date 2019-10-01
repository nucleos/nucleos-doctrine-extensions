<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Adapter\ORM;

use Core23\Doctrine\Tests\Fixtures\DemoEntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class EntityManagerTraitTest extends TestCase
{
    public function testCreateQueryBuilder(): void
    {
        $queryBuilder = $this->prophesize(QueryBuilder::class);

        $repository = $this->prophesize(EntityRepository::class);
        $repository->createQueryBuilder('alias', 'someindex')
            ->willReturn($queryBuilder)
        ;

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('foo')
            ->willReturn($repository)
        ;

        $registry = $this->prophesize(ManagerRegistry::class);
        $registry->getManagerForClass('foo')
            ->willReturn($objectManager)
        ;

        $manager = new DemoEntityManager('foo', $registry->reveal());

        static::assertSame($queryBuilder->reveal(), $manager->getQueryBuilder('alias', 'someindex'));
    }
}
