<?php

namespace Byss\Bundle\AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * DI for the Byss bundle
 * @author    Tomasz Sanecki (t.sanecki@byss.pl)
 * @copyright 2018 Byss
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ByssAppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $loader->load('attribute_icons.yml');
        $loader->load('providers.yml');
        $loader->load('entities.yml');
        $loader->load('factories.yml');
        $loader->load('comparators.yml');
        $loader->load('datagrid/attribute_types.yml');
    }
}

