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

final class ConfirmableTraitTest extends TestCase
{
    /**
     * @var ClassWithAllProperties
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = new ClassWithAllProperties();
    }

    public function testIsConfirmedWithDefault(): void
    {
        self::assertNull($this->trait->getConfirmedAt());
    }

    public function testGetConfirmedAtWithDefault(): void
    {
        self::assertFalse($this->trait->isConfirmed());
    }

    public function testSetUnConfirmed(): void
    {
        $this->trait->setConfirmed(true);

        self::assertTrue($this->trait->isConfirmed());
        self::assertNotNull($this->trait->getConfirmedAt());

        $this->trait->setConfirmed(false);

        self::assertFalse($this->trait->isConfirmed());
        self::assertNull($this->trait->getConfirmedAt());
    }

    public function testSetConfirmedAt(): void
    {
        $now = new DateTime();

        $this->trait->setConfirmedAt($now);

        self::assertSame($now, $this->trait->getConfirmedAt());
        self::assertTrue($this->trait->isConfirmed());
    }
}
