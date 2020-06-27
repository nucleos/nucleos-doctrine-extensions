<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Model\Traits;

use DateTime;
use Nucleos\Doctrine\Model\Traits\DeleteableTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteableTraitTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = $this->getMockForTrait(DeleteableTrait::class);
    }

    public function testIsDeletedWithDefault(): void
    {
        static::assertNull($this->trait->getDeletedAt());
    }

    public function testGetDeletedAtWithDefault(): void
    {
        static::assertFalse($this->trait->isDeleted());
    }

    public function testSetUnDeleted(): void
    {
        $this->trait->setDeleted(true);

        static::assertTrue($this->trait->isDeleted());
        static::assertNotNull($this->trait->getDeletedAt());

        $this->trait->setDeleted(false);

        static::assertFalse($this->trait->isDeleted());
        static::assertNull($this->trait->getDeletedAt());
    }

    public function testSetDeletedAt(): void
    {
        $now = new DateTime();

        $this->trait->setDeletedAt($now);

        static::assertSame($now, $this->trait->getDeletedAt());
        static::assertTrue($this->trait->isDeleted());
    }
}
