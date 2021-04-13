<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Model\Traits;

use Nucleos\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use PHPUnit\Framework\TestCase;

final class SortableTraitTest extends TestCase
{
    /**
     * @var ClassWithAllProperties
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = new ClassWithAllProperties();
    }

    public function testPositionWithDefault(): void
    {
        static::assertNull($this->trait->getPosition());
    }

    public function testPosition(): void
    {
        $this->trait->setPosition(14);

        static::assertSame(14, $this->trait->getPosition());
    }

    public function testGetPositionGroup(): void
    {
        static::assertSame([], $this->trait->getPositionGroup());
    }
}
