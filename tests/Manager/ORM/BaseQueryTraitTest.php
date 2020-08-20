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
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class BaseQueryTraitTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var DemoEntityManager
     */
    private $manager;

    protected function setUp(): void
    {
        $repository = $this->prophesize(EntityRepository::class);

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('foo')
            ->willReturn($repository)
        ;

        $registry = $this->prophesize(ManagerRegistry::class);
        $registry->getManagerForClass('foo')
            ->willReturn($objectManager)
        ;

        $this->manager = new DemoEntityManager('foo', $registry->reveal());
    }

    public function testAddOrder(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'asc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
            ['position'],
            'myalias'
        ));
    }

    public function testAddOrderWithOrder(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
            ['position'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithMultpleSorts(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc')
            ->shouldBeCalled()
        ;
        $builder->addOrderBy('myalias.otherfield', 'desc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
            ['position', 'otherfield'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithMultpleSortAndOrders(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('myalias.position', 'desc')
            ->shouldBeCalled()
        ;
        $builder->addOrderBy('myalias.otherfield', 'asc')
            ->shouldBeCalled()
        ;
        $builder->addOrderBy('myalias.foo', 'desc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
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
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('child.position', 'desc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
            ['child.position'],
            'myalias',
            [],
            'desc'
        ));
    }

    public function testAddOrderWithAliasChildOrder(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);

        $builder->addOrderBy('foo.position', 'desc')
            ->shouldBeCalled()
        ;

        static::assertSame($builder->reveal(), $this->manager->addOrderToQueryBuilder(
            $builder->reveal(),
            ['f.position'],
            'myalias',
            ['f' => 'foo'],
            'desc'
        ));
    }
}
