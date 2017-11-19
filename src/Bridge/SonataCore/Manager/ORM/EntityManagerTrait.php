<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Bridge\SonataCore\Manager\ORM;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
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
        /* @noinspection PhpUndefinedMethodInspection */
        return $this->getRepository()
            ->createQueryBuilder($alias, $indexBy);
    }

    /**
     * Returns the related Object Repository.
     *
     * @return ObjectRepository
     */
    abstract protected function getRepository();
}
