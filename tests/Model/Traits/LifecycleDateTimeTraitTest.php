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

final class LifecycleDateTimeTraitTest extends TestCase
{
    /**
     * @var ClassWithAllProperties
     */
    private $trait;

    protected function setUp(): void
    {
        $this->trait = new ClassWithAllProperties();
    }

    public function testIsCreatedWithDefault(): void
    {
        self::assertNull($this->trait->getCreatedAt());
    }

    public function testIsUpdatedWithDefault(): void
    {
        self::assertNull($this->trait->getCreatedAt());
    }

    public function testSetCreated(): void
    {
        $now = new DateTime();

        $this->trait->setCreatedAt($now);

        self::assertSame($now, $this->trait->getCreatedAt());

        $this->trait->setCreatedAt(null);

        self::assertNull($this->trait->getCreatedAt());
    }

    public function testSetUpdated(): void
    {
        $now = new DateTime();

        $this->trait->setUpdatedAt($now);

        self::assertSame($now, $this->trait->getUpdatedAt());

        $this->trait->setUpdatedAt(null);

        self::assertNull($this->trait->getUpdatedAt());
    }
}
