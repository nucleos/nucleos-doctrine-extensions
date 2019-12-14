<?php

declare(strict_types=1);

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

final class LifecycleDateTimeTraitTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        $this->trait = $this->getMockForTrait(LifecycleDateTimeTrait::class);
    }

    public function testIsCreatedWithDefault(): void
    {
        static::assertNull($this->trait->getCreatedAt());
    }

    public function testIsUpdatedWithDefault(): void
    {
        static::assertNull($this->trait->getCreatedAt());
    }

    public function testSetCreated(): void
    {
        $now = new DateTime();

        $this->trait->setCreatedAt($now);

        static::assertSame($now, $this->trait->getCreatedAt());

        $this->trait->setCreatedAt(null);

        static::assertNull($this->trait->getCreatedAt());
    }

    public function testSetUpdated(): void
    {
        $now = new DateTime();

        $this->trait->setUpdatedAt($now);

        static::assertSame($now, $this->trait->getUpdatedAt());

        $this->trait->setUpdatedAt(null);

        static::assertNull($this->trait->getUpdatedAt());
    }
}
