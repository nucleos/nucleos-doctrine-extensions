<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Manager;

use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

trait SearchQueryTrait
{
    /**
     * Creates a like search for a given field and text values.
     *
     * @param QueryBuilder $qb
     * @param string       $field
     * @param array        $values
     * @param bool         $strict
     *
     * @return Composite
     */
    public function searchWhere(QueryBuilder $qb, string $field, array $values, bool $strict = false): Composite
    {
        $orx = $qb->expr()->orX();
        foreach ($values as $index => $word) {
            $orx->add(sprintf('%s = :name'.$index, $field));
            $qb->setParameter('name'.$index, $word);

            if (!$strict) {
                $this->buildLikeExpressions($qb, $orx, $field, $word, $index);
            }
        }

        return $orx;
    }

    /**
     * @param QueryBuilder $qb
     * @param Orx          $orx
     * @param string       $field
     * @param string       $word
     * @param int          $index
     */
    private function buildLikeExpressions(QueryBuilder $qb, Orx $orx, string $field, string $word, int $index): void
    {
        $orx->add(sprintf('%s LIKE :name'.$index.'_any', $field));
        $orx->add(sprintf('%s LIKE :name'.$index.'_pre', $field));
        $orx->add(sprintf('%s LIKE :name'.$index.'_suf', $field));

        $qb->setParameter('name'.$index.'_any', '% '.$word.' %');
        $qb->setParameter('name'.$index.'_pre', '% '.$word);
        $qb->setParameter('name'.$index.'_suf', $word.' %');
    }
}
