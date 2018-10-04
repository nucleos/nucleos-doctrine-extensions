<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Manager\ORM;

use Doctrine\ORM\QueryBuilder;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

trait BaseQueryTrait
{
    /**
     * Builds a pager for a given query builder.
     *
     * @param QueryBuilder $builder
     * @param int          $limit
     * @param int          $page
     *
     * @return PagerInterface
     */
    public function createPager(QueryBuilder $builder, int $limit, int $page): PagerInterface
    {
        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($builder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * @param QueryBuilder              $builder
     * @param array<int|string, string> $sort
     * @param string                    $defaultEntity
     * @param array<string,string>      $aliasMapping
     * @param string                    $defaultOrder
     *
     * @return QueryBuilder
     */
    final protected function addOrder(QueryBuilder $builder, array $sort, string $defaultEntity, array $aliasMapping = [], string $defaultOrder = 'asc'): QueryBuilder
    {
        foreach ($sort as $field => $order) {
            if (\is_int($field)) {
                $field = $order;
                $order = $defaultOrder;
            }

            $fieldSpl = explode('.', $field);

            if (\count($fieldSpl) > 2) {
                continue;
            }

            $table = $defaultEntity;

            // Map entity to table name
            if (2 === \count($fieldSpl)) {
                foreach ($aliasMapping as $k => $v) {
                    if ($fieldSpl[0] === $k) {
                        $table = $v;
                        $field = $fieldSpl[1];

                        break;
                    }
                }
            }

            $builder->addOrderBy($table.'.'.$field, $order);
        }

        return $builder;
    }
}
