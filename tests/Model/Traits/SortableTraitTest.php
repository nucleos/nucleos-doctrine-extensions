<?php

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
    public function testPositionWithDefault(): void
    {
        $model = $this->createTraitMock();

        static::assertNull($model->getPosition());
    }

    public function testPosition(): void
    {
        $model = $this->createTraitMock();
        $model->setPosition(14);

        static::assertSame(14, $model->getPosition());
    }

    public function testGetPositionGroup(): void
    {
        $model = $this->createTraitMock();

        static::assertSame([], $model->getPositionGroup());
    }

    private function createTraitMock()
    {
        return $this->getMockForTrait(SortableTrait::class);
    }
}
