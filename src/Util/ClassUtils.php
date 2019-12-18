<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Util;

use InvalidArgumentException;
use ReflectionClass;

/**
 * @template-covariant T of object
 */
final class ClassUtils
{
    /**
     * @param ReflectionClass<T> $reflection
     * @param class-string<T>    $class
     */
    public static function containsTrait(ReflectionClass $reflection, string $class): bool
    {
        if (!trait_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" is not a valid trait', $class));
        }

        do {
            $traits = $reflection->getTraitNames();

            if (\in_array($class, $traits, true)) {
                return true;
            }

            foreach ($reflection->getTraits() as $reflTraits) {
                if (static::containsTrait($reflTraits, $class)) {
                    return true;
                }
            }
        } while ($reflection = $reflection->getParentClass());

        return false;
    }
}
