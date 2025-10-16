<?php
declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $confDir = \dirname(__DIR__).'/config';

        $container->import($confDir.'/packages/*.yaml');

        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $container->import($confDir.'/packages/'.$this->environment.'/*.yaml');
        }

        if (file_exists($confDir.'/services.yaml')) {
            $container->import($confDir.'/services.yaml');
        }
        if (file_exists($confDir.'/services_'.$this->environment.'.yaml')) {
            $container->import($confDir.'/services_'.$this->environment.'.yaml');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../src/Controller/', 'attribute');
    }
}
