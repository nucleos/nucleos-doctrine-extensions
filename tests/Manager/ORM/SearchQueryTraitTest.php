<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Manager\ORM;

use Core23\Doctrine\Tests\Fixtures\DemoEntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class SearchQueryTraitTest extends TestCase
{
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

    public function testSearchWhere(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo')
            ->shouldBeCalled()
        ;
        $builder->setParameter('name0_any', '% foo %')
            ->shouldBeCalled()
        ;
        $builder->setParameter('name0_pre', '% foo')
            ->shouldBeCalled()
        ;
        $builder->setParameter('name0_suf', 'foo %')
            ->shouldBeCalled()
        ;

        $orx->add('field = :name0')
            ->shouldBeCalled()
        ;
        $orx->add('field LIKE :name0_any')
            ->shouldBeCalled()
        ;
        $orx->add('field LIKE :name0_pre')
            ->shouldBeCalled()
        ;
        $orx->add('field LIKE :name0_suf')
            ->shouldBeCalled()
        ;

        static::assertSame($orx->reveal(), $this->manager->searchWhereQueryBuilder(
            $builder->reveal(),
            'field',
            ['foo']
        ));
    }

    public function testStrictSearchWhere(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo')
            ->shouldBeCalled()
        ;
        $orx->add('field = :name0')
            ->shouldBeCalled()
        ;

        static::assertSame($orx->reveal(), $this->manager->searchWhereQueryBuilder(
            $builder->reveal(),
            'field',
            ['foo'],
            true
        ));
    }

    public function testSearchWhereMultipleValues(): void
    {
        $builder = $this->prophesize(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo')
            ->shouldBeCalled()
        ;
        $builder->setParameter('name1', 'bar')
            ->shouldBeCalled()
        ;
        $builder->setParameter('name2', 'baz')
            ->shouldBeCalled()
        ;
        $orx->add('field = :name0')
            ->shouldBeCalled()
        ;
        $orx->add('field = :name1')
            ->shouldBeCalled()
        ;
        $orx->add('field = :name2')
            ->shouldBeCalled()
        ;

        static::assertSame($orx->reveal(), $this->manager->searchWhereQueryBuilder(
            $builder->reveal(),
            'field',
            ['foo', 'bar', 'baz'],
            true
        ));
    }

    private function prepareOrx(ObjectProphecy $builder): ObjectProphecy
    {
        $orx = $this->prophesize(Orx::class);

        $expr = $this->prophesize(Expr::class);
        $expr->orX()
            ->willReturn($orx)
        ;

        $builder->expr()
            ->willReturn($expr)
        ;

        return $orx;
    }
}
