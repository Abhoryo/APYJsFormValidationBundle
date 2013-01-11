<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class APYJsFormValidationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->process($this->getConfigTree(), $configs);

        if (isset($config['check_mode'])) {
            // throw an informative message about option removal
            // TODO: remove this in some future
            throw new InvalidConfigurationException("Option 'check_mode' is removed, use a new 'check_modes' (note trailing 's') option instead. Refer to bundle documentation for details.");
        }

        $container->setParameter('apy_js_form_validation.enabled', $config['enabled']);
        $container->setParameter('apy_js_form_validation.yui_js', $config['yui_js']);
        $container->setParameter('apy_js_form_validation.check_modes', $config['check_modes']);
        $container->setParameter('apy_js_form_validation.script_directory', $config['script_directory']);
        $container->setParameter('apy_js_form_validation.validation_bundle', $config['validation_bundle']);
        $container->setParameter('apy_js_form_validation.javascript_framework', $config['javascript_framework']);
        $container->setParameter('apy_js_form_validation.warmer_routes', $config['warmer_routes']);
        $container->setParameter('apy_js_form_validation.identifier_field', $config['identifier_field']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('controllers.xml');
    }

    private function getConfigTree()
    {
        $tb = new TreeBuilder();
        return $tb
            ->root('apy_js_form_validation')
                ->children()
                    ->booleanNode('enabled')->defaultValue(true)->end()
                    ->booleanNode('yui_js')->defaultValue(false)->end()
                    // Without this line Symfony will throw non-informative exception on 'check_mode'
                    // option is not present in a tree before our explanatory exception can be thrown.
                    // TODO: remove this in some future
                    ->scalarNode('check_mode')->end()
                    ->arrayNode('check_modes')
                        ->defaultValue(array('submit', 'blur'))
                        ->prototype('scalar')
                            ->validate()
                                ->ifNotInArray(array('submit', 'blur', 'change'))
                                ->thenInvalid('%s is not a valid validation mode. Refer to bundle documentation.')
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('validation_bundle')->defaultValue('APYJsFormValidationBundle')->end()
                    ->scalarNode('javascript_framework')->defaultValue('jquery')->end()
                    ->scalarNode('script_directory')->defaultValue('bundles/jsformvalidation/js/')->end()
                    ->arrayNode('warmer_routes')
                        ->canBeUnset()
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('identifier_field')->defaultValue('jsfv_identifier')->end()
                ->end()
            ->end()
            ->buildTree();
    }
}
