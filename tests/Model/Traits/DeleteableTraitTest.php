<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Model\Traits;

use Core23\Doctrine\Model\Traits\DeleteableTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

class DeleteableTraitTest extends TestCase
{
    public function testIsDeletedWithDefault(): void
    {
        $model = $this->createTraitMock();

        $this->assertNull($model->getDeletedAt());
    }

    public function testGetDeletedAtWithDefault(): void
    {
        $model = $this->createTraitMock();

        $this->assertFalse($model->isDeleted());
    }

    public function testSetUnDeleted(): void
    {
        $model = $this->createTraitMock();
        $model->setDeleted(true);

        $this->assertTrue($model->isDeleted());
        $this->assertNotNull($model->getDeletedAt());

        $model->setDeleted(false);

        $this->assertFalse($model->isDeleted());
        $this->assertNull($model->getDeletedAt());
    }

    public function testSetDeletedAt(): void
    {
        $now = new DateTime();

        $model = $this->createTraitMock();
        $model->setDeletedAt($now);

        $this->assertSame($now, $model->getDeletedAt());
        $this->assertTrue($model->isDeleted());
    }

    private function createTraitMock()
    {
        return $this->getMockForTrait(DeleteableTrait::class);
    }
}
