<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\EventListener\ORM;

use Core23\Doctrine\EventListener\ORM\TablePrefixEventListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;

class TablePrefixEventListenerTest extends TestCase
{
    public function testItIsInstantiable(): void
    {
        $listener = new TablePrefixEventListener('acme_');

        $this->assertInstanceOf(EventSubscriber::class, $listener);
    }

    public function testGetSubscribedEvents(): void
    {
        $listener = new TablePrefixEventListener('acme_');

        $this->assertSame([
            Events::loadClassMetadata,
        ], $listener->getSubscribedEvents());
    }
}
