<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Fixtures;

use Core23\Doctrine\Adapter\ORM\AbstractEntityManager;
use Core23\Doctrine\Manager\ORM\SearchQueryTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder;

final class DemoEntityManager extends AbstractEntityManager
{
    use SearchQueryTrait;

    /**
     * @return EntityManager|QueryBuilder
     */
    public function getQueryBuilder(string $alias, ?string $indexBy = null)
    {
        return $this->createQueryBuilder($alias, $indexBy);
    }

    public function searchWhereQueryBuilder(QueryBuilder $qb, string $field, array $values, bool $strict = false): Composite
    {
        return $this->searchWhere($qb, $field, $values, $strict);
    }

    public function addOrderToQueryBuilder(QueryBuilder $builder, array $sort, string $defaultEntity, array $aliasMapping = [], string $defaultOrder = 'asc'): QueryBuilder
    {
        return $this->addOrder($builder, $sort, $defaultEntity, $aliasMapping, $defaultOrder);
    }
}
