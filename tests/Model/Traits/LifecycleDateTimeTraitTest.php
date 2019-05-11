<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Model\Traits;

use Core23\Doctrine\Model\Traits\LifecycleDateTimeTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

class LifecycleDateTimeTraitTest extends TestCase
{
    public function testIsCreatedWithDefault(): void
    {
        $model = $this->createTraitMock();

        static::assertNull($model->getCreatedAt());
    }

    public function testIsUpdatedWithDefault(): void
    {
        $model = $this->createTraitMock();

        static::assertNull($model->getCreatedAt());
    }

    public function testSetCreated(): void
    {
        $now = new DateTime();

        $model = $this->createTraitMock();
        $model->setCreatedAt($now);

        static::assertSame($now, $model->getCreatedAt());

        $model->setCreatedAt(null);

        static::assertNull($model->getCreatedAt());
    }

    public function testSetUpdated(): void
    {
        $now = new DateTime();

        $model = $this->createTraitMock();
        $model->setUpdatedAt($now);

        static::assertSame($now, $model->getUpdatedAt());

        $model->setUpdatedAt(null);

        static::assertNull($model->getUpdatedAt());
    }

    private function createTraitMock()
    {
        return $this->getMockForTrait(LifecycleDateTimeTrait::class);
    }
}
