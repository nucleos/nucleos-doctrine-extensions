<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\EventListener\ORM;

use Doctrine\Common\EventSubscriber;
use ReflectionClass;

abstract class AbstractListener implements EventSubscriber
{
    /**
     * @param ReflectionClass $reflection
     * @param string          $class
     *
     * @return bool
     */
    final protected function containsTrait(ReflectionClass $reflection, string $class): bool
    {
        do {
            $traits = $reflection->getTraitNames();

            if (in_array($class, $traits)) {
                return true;
            }

            foreach ($reflection->getTraits() as $reflTraits) {
                if ($this->containsTrait($reflTraits, $class)) {
                    return true;
                }
            }
        } while ($reflection = $reflection->getParentClass());

        return false;
    }
}
