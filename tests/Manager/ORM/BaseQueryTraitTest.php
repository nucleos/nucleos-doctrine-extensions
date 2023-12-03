<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Manager\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Nucleos\Doctrine\Tests\Fixtures\DemoEntityManager;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\TestCase;

final class BaseQueryTraitTest extends TestCase
{
    /**
     * @var DemoEntityManager
     */
    private $manager;

    protected function setUp(): void
    {
        $repository = $this->createMock(EntityRepository::class);

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->method('getRepository')->with(EmptyClass::class)
            ->willReturn($repository)
        ;

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->with(EmptyClass::class)
            ->willReturn($objectManager)
        ;

        $this->manager = new DemoEntityManager(EmptyClass::class, $registry);
    }

    public function testAddOrder(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'asc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            ['position'],
            'myalias'
        ));
    }

    public function testAddOrderWithOrder(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            ['position'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithMultpleSorts(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc');
        $builder->addOrderBy('myalias.otherfield', 'desc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            ['position', 'otherfield'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithMultpleSortAndOrders(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc');
        $builder->addOrderBy('myalias.otherfield', 'asc');
        $builder->addOrderBy('myalias.foo', 'desc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            [
                'position',
                'otherfield' => 'asc',
                'foo'        => 'desc',
            ],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithChildOrder(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('child.position', 'desc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            ['child.position'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithAliasChildOrder(): void
    {
        $builder = $this->createMock(QueryBuilder::class);

        $builder->addOrderBy('foo.position', 'desc');

        self::assertSame($builder, $this->manager->addOrderToQueryBuilder(
            $builder,
            ['f.position'],
            'myalias',
            ['f' => 'foo'],
            'desc'
        ));
    }
}
