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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfirmableTraitTest extends TestCase
{
    public function testIsConfirmedWithDefault(): void
    {
        $model = $this->createTraitMock();

        $this->assertNull($model->getConfirmedAt());
    }

    public function testGetConfirmedAtWithDefault(): void
    {
        $model = $this->createTraitMock();

        $this->assertFalse($model->isConfirmed());
    }

    public function testSetUnConfirmed(): void
    {
        $model = $this->createTraitMock();
        $model->setConfirmed(true);

        $this->assertTrue($model->isConfirmed());
        $this->assertNotNull($model->getConfirmedAt());

        $model->setConfirmed(false);

        $this->assertFalse($model->isConfirmed());
        $this->assertNull($model->getConfirmedAt());
    }

    public function testSetConfirmedAt(): void
    {
        $now = new DateTime();

        $model = $this->createTraitMock();
        $model->setConfirmedAt($now);

        $this->assertSame($now, $model->getConfirmedAt());
        $this->assertTrue($model->isConfirmed());
    }

    private function createTraitMock()
    {
        return $this->getMockForTrait(ConfirmableTrait::class);
    }
}
