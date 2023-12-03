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
use Nucleos\Doctrine\Tests\Fixtures\ClassWithAllProperties;
use PHPUnit\Framework\TestCase;

final class DeleteableTraitTest extends TestCase
{
    /**
     * @var ClassWithAllProperties
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = new ClassWithAllProperties();
    }

    public function testIsDeletedWithDefault(): void
    {
        self::assertNull($this->trait->getDeletedAt());
    }

    public function testGetDeletedAtWithDefault(): void
    {
        self::assertFalse($this->trait->isDeleted());
    }

    public function testSetUnDeleted(): void
    {
        $this->trait->setDeleted(true);

        self::assertTrue($this->trait->isDeleted());
        self::assertNotNull($this->trait->getDeletedAt());

        $this->trait->setDeleted(false);

        self::assertFalse($this->trait->isDeleted());
        self::assertNull($this->trait->getDeletedAt());
    }

    public function testSetDeletedAt(): void
    {
        $now = new DateTime();

        $this->trait->setDeletedAt($now);

        self::assertSame($now, $this->trait->getDeletedAt());
        self::assertTrue($this->trait->isDeleted());
    }
}
