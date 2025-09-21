<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*' . self::CONFIG_EXTS);
        $container->import('../config/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS);

        $container->import('../config/{services}' . self::CONFIG_EXTS);
        $container->import('../config/{services}_' . $this->environment . self::CONFIG_EXTS);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS);
        $routes->import('../config/{routes}/*' . self::CONFIG_EXTS);
        $routes->import('../config/{routes}' . self::CONFIG_EXTS);
    }
}
