<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Model\Traits;

use Core23\Doctrine\Model\Traits\ConfirmableTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

final class ConfirmableTraitTest extends TestCase
{
    public function testIsConfirmedWithDefault(): void
    {
        $model = $this->createTraitMock();

        static::assertNull($model->getConfirmedAt());
    }

    public function testGetConfirmedAtWithDefault(): void
    {
        $model = $this->createTraitMock();

        static::assertFalse($model->isConfirmed());
    }

    public function testSetUnConfirmed(): void
    {
        $model = $this->createTraitMock();
        $model->setConfirmed(true);

        static::assertTrue($model->isConfirmed());
        static::assertNotNull($model->getConfirmedAt());

        $model->setConfirmed(false);

        static::assertFalse($model->isConfirmed());
        static::assertNull($model->getConfirmedAt());
    }

    public function testSetConfirmedAt(): void
    {
        $now = new DateTime();

        $model = $this->createTraitMock();
        $model->setConfirmedAt($now);

        static::assertSame($now, $model->getConfirmedAt());
        static::assertTrue($model->isConfirmed());
    }

    private function createTraitMock()
    {
        return $this->getMockForTrait(ConfirmableTrait::class);
    }
}
