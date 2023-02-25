<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Bridge\Symfony\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Nucleos\Doctrine\Bridge\Symfony\Bundle\NucleosDoctrineBundle;
use Nucleos\Doctrine\Tests\Bridge\Symfony\App\Controller\SampleTestController;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();

        yield new DoctrineBundle();

        yield new NucleosDoctrineBundle();

        yield new DoctrineMigrationsBundle();
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureRoutes($routes): void
    {
        if ($routes instanceof RoutingConfigurator) {
            $routes
                ->add('sample', '/test')
                ->controller(SampleTestController::class)
            ;

            return;
        }

        $routes->add('/test', SampleTestController::class);
    }

    protected function configureContainer($container, $loader): void
    {
        if ($container instanceof ContainerConfigurator) {
            $container->import(__DIR__.'/config/config.php');

            return;
        }

        $loader->load(__DIR__.'/config/config.php');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/app-bundle/var/';
    }
}
