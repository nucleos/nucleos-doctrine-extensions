<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Bridge\SonataCore\Manager\ORM;

use Core23\DoctrineExtensions\Manager\ORM\BaseQueryTrait;
use Sonata\CoreBundle\Model\BaseEntityManager as SonataBaseEntityManager;

abstract class AbstractEntityManager extends SonataBaseEntityManager
{
    use EntityManagerTrait, BaseQueryTrait;
}
