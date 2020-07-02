<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\Doctrine\EventListener\ORM\ConfirmableListener;
use Nucleos\Doctrine\EventListener\ORM\DeletableListener;
use Nucleos\Doctrine\EventListener\ORM\LifecycleDateListener;
use Nucleos\Doctrine\EventListener\ORM\SortableListener;
use Nucleos\Doctrine\EventListener\ORM\TablePrefixEventListener;
use Nucleos\Doctrine\EventListener\ORM\UniqueActiveListener;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(ConfirmableListener::class)
            ->tag('doctrine.event_subscriber')

        ->set(DeletableListener::class)
            ->tag('doctrine.event_subscriber')

        ->set(LifecycleDateListener::class)
            ->tag('doctrine.event_subscriber')

        ->set(SortableListener::class)
            ->tag('doctrine.event_subscriber')
            ->args([
                new Reference('property_accessor'),
            ])

        ->set(UniqueActiveListener::class)
            ->tag('doctrine.event_subscriber')
            ->args([
                new Reference('property_accessor'),
            ])

        ->set(TablePrefixEventListener::class)
            ->tag('doctrine.event_subscriber')
            ->args([
                new Parameter('nucleos_doctrine.table.prefix'),
            ])

        ;
};
