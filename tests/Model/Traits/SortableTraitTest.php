<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Model\Traits;

use Core23\Doctrine\Model\Traits\SortableTrait;
use PHPUnit\Framework\TestCase;

final class SortableTraitTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        $this->trait = $this->getMockForTrait(SortableTrait::class);
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
