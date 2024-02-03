<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\Events;
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
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])

        ->set(DeletableListener::class)
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])

        ->set(LifecycleDateListener::class)
            ->tag('doctrine.event_listener', [
                'event' => Events::prePersist,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::preUpdate,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])

        ->set(SortableListener::class)
            ->tag('doctrine.event_listener', [
                'event' => Events::prePersist,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::preUpdate,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::preRemove,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])
            ->args([
                new Reference('property_accessor'),
            ])

        ->set(UniqueActiveListener::class)
            ->tag('doctrine.event_listener', [
                'event' => Events::prePersist,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::preUpdate,
            ])
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])
            ->args([
                new Reference('property_accessor'),
            ])

        ->set(TablePrefixEventListener::class)
            ->tag('doctrine.event_listener', [
                'event' => Events::loadClassMetadata,
            ])
            ->args([
                new Parameter('nucleos_doctrine.table.prefix'),
            ])
    ;
};
