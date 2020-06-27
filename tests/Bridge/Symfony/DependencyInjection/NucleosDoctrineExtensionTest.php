<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Bridge\Symfony\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Nucleos\Doctrine\Bridge\Symfony\DependencyInjection\NucleosDoctrineExtension;
use Nucleos\Doctrine\EventListener\ORM\ConfirmableListener;
use Nucleos\Doctrine\EventListener\ORM\DeletableListener;
use Nucleos\Doctrine\EventListener\ORM\LifecycleDateListener;
use Nucleos\Doctrine\EventListener\ORM\SortableListener;
use Nucleos\Doctrine\EventListener\ORM\TablePrefixEventListener;
use Nucleos\Doctrine\EventListener\ORM\UniqueActiveListener;

final class NucleosDoctrineExtensionTest extends AbstractExtensionTestCase
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

    protected function getContainerExtensions(): array
    {
        return [
            new NucleosDoctrineExtension(),
        ];
    }
}
