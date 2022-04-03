<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\Doctrine\Tests\Bridge\Symfony\App\Controller\SampleTestController;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', ['secret' => 'MySecret']);

    $containerConfigurator->extension('framework', ['test' => true]);

    $containerConfigurator->extension('doctrine', ['dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/data.db', 'logging' => false]]);

    $containerConfigurator->extension('doctrine_migrations', [
        'migrations_paths' => [
            'Nucleos\Doctrine\Tests\Bridge\Symfony\App\Migrations' => \dirname(__DIR__).'/Migrations',
        ],
    ]);

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(SampleTestController::class)
        ->tag('controller.service_arguments')
    ;
};
