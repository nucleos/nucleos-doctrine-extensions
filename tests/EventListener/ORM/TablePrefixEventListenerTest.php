<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\EventListener\ORM;

use Core23\Doctrine\EventListener\ORM\TablePrefixEventListener;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;

final class TablePrefixEventListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new TablePrefixEventListener('acme_');

        static::assertSame([
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }
}
