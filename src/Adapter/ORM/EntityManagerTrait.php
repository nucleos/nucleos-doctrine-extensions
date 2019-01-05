<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Adapter\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

trait EntityManagerTrait
{
    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from
     *
     * @return QueryBuilder|EntityManager
     */
    final protected function createQueryBuilder(string $alias, string $indexBy = null)
    {
        return $this->getRepository()
            ->createQueryBuilder($alias, $indexBy);
    }

    /**
     * Returns the related Object Repository.
     *
     * @return EntityRepository
     */
    abstract protected function getRepository();
}
