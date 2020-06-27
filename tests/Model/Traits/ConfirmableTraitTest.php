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
use Nucleos\Doctrine\Model\Traits\ConfirmableTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConfirmableTraitTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = $this->getMockForTrait(ConfirmableTrait::class);
    }

    public function testIsConfirmedWithDefault(): void
    {
        static::assertNull($this->trait->getConfirmedAt());
    }

    public function testGetConfirmedAtWithDefault(): void
    {
        static::assertFalse($this->trait->isConfirmed());
    }

    public function testSetUnConfirmed(): void
    {
        $this->trait->setConfirmed(true);

        static::assertTrue($this->trait->isConfirmed());
        static::assertNotNull($this->trait->getConfirmedAt());

        $this->trait->setConfirmed(false);

        static::assertFalse($this->trait->isConfirmed());
        static::assertNull($this->trait->getConfirmedAt());
    }

    public function testSetConfirmedAt(): void
    {
        $now = new DateTime();

        $this->trait->setConfirmedAt($now);

        static::assertSame($now, $this->trait->getConfirmedAt());
        static::assertTrue($this->trait->isConfirmed());
    }
}
