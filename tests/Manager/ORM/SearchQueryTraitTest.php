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
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Nucleos\Doctrine\Tests\Fixtures\DemoEntityManager;
use Nucleos\Doctrine\Tests\Fixtures\EmptyClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SearchQueryTraitTest extends TestCase
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

    public function testSearchWhere(): void
    {
        $builder = $this->createMock(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo');
        $builder->setParameter('name0_any', '% foo %');
        $builder->setParameter('name0_pre', '% foo');
        $builder->setParameter('name0_suf', 'foo %');

        $orx->add('field = :name0');
        $orx->add('field LIKE :name0_any');
        $orx->add('field LIKE :name0_pre');
        $orx->add('field LIKE :name0_suf');

        static::assertSame($orx, $this->manager->searchWhereQueryBuilder(
            $builder,
            'field',
            ['foo']
        ));
    }

    public function testStrictSearchWhere(): void
    {
        $builder = $this->createMock(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo');
        $orx->add('field = :name0');

        static::assertSame($orx, $this->manager->searchWhereQueryBuilder(
            $builder,
            'field',
            ['foo'],
            true
        ));
    }

    public function testSearchWhereMultipleValues(): void
    {
        $builder = $this->createMock(QueryBuilder::class);
        $orx     = $this->prepareOrx($builder);

        $builder->setParameter('name0', 'foo');
        $builder->setParameter('name1', 'bar');
        $builder->setParameter('name2', 'baz');
        $orx->add('field = :name0');
        $orx->add('field = :name1');
        $orx->add('field = :name2');

        static::assertSame($orx, $this->manager->searchWhereQueryBuilder(
            $builder,
            'field',
            ['foo', 'bar', 'baz'],
            true
        ));
    }

    /**
     * @param MockObject&QueryBuilder $builder
     *
     * @return MockObject&Orx
     */
    private function prepareOrx(QueryBuilder $builder): MockObject
    {
        $orx = $this->createMock(Orx::class);

        $expr = $this->createMock(Expr::class);
        $expr->method('orX')
            ->willReturn($orx)
        ;

        $builder->method('expr')
            ->willReturn($expr)
        ;

        return $orx;
    }
}
