<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Util;

use Core23\Doctrine\Tests\Fixtures\ChildTrait;
use Core23\Doctrine\Tests\Fixtures\ClassWithTrait;
use Core23\Doctrine\Tests\Fixtures\EmptyClass;
use Core23\Doctrine\Tests\Fixtures\TestTrait;
use Core23\Doctrine\Util\ClassUtils;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ClassUtilsTest extends TestCase
{
    public function testContainsTrait(): void
    {
        $reflection = new ReflectionClass(ClassWithTrait::class);

        static::assertTrue(ClassUtils::containsTrait($reflection, TestTrait::class));
    }

    public function testContainsChildTrait(): void
    {
        $reflection = new ReflectionClass(ClassWithTrait::class);

        static::assertTrue(ClassUtils::containsTrait($reflection, ChildTrait::class));
    }

    public function testContainsTraitWithNormalClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "Core23\Doctrine\Tests\Fixtures\EmptyClass" is not a valid trait');

        $reflection = new ReflectionClass(ClassWithTrait::class);

        static::assertTrue(ClassUtils::containsTrait($reflection, EmptyClass::class));
    }

    public function testContainsNoTrait(): void
    {
        $reflection = new ReflectionClass(EmptyClass::class);

        static::assertFalse(ClassUtils::containsTrait($reflection, TestTrait::class));
    }
}
