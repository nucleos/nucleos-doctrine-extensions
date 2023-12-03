<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\EventListener\ORM;

use Doctrine\ORM\Events;
use Nucleos\Doctrine\EventListener\ORM\TablePrefixEventListener;
use PHPUnit\Framework\TestCase;

final class TablePrefixEventListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new TablePrefixEventListener('acme_');

        self::assertSame([
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }
}
