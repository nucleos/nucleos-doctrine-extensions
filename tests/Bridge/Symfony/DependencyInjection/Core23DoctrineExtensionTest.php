<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Bridge\Symfony\DependencyInjection;

use Core23\Doctrine\Bridge\Symfony\DependencyInjection\Core23DoctrineExtension;
use Core23\Doctrine\EventListener\ORM\ConfirmableListener;
use Core23\Doctrine\EventListener\ORM\DeletableListener;
use Core23\Doctrine\EventListener\ORM\LifecycleDateListener;
use Core23\Doctrine\EventListener\ORM\SortableListener;
use Core23\Doctrine\EventListener\ORM\TablePrefixEventListener;
use Core23\Doctrine\EventListener\ORM\UniqueActiveListener;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class Core23DoctrineExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(ConfirmableListener::class);
        $this->assertContainerBuilderHasService(DeletableListener::class);
        $this->assertContainerBuilderHasService(LifecycleDateListener::class);
        $this->assertContainerBuilderHasService(SortableListener::class);
        $this->assertContainerBuilderHasService(UniqueActiveListener::class);
        $this->assertContainerBuilderHasService(TablePrefixEventListener::class);
    }

    protected function getContainerExtensions()
    {
        return [
            new Core23DoctrineExtension(),
        ];
    }
}
