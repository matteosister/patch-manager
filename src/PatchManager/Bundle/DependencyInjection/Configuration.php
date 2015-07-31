<?php

namespace Cypress\PatchManager\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('patch_manager');
        $rootNode
            ->children()
                ->arrayNode('handlers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('data')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity_manager')->defaultValue('default')->end()
                                ->scalarNode('doctrine')->defaultTrue()->end()
                                ->scalarNode('magic_call')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->scalarNode('state_machine')->end()
                    ->end()
                ->end()
                ->scalarNode('dispatch_events')->defaultTrue()->end()
                ->scalarNode('strict_mode')->defaultFalse()->end()
                ->scalarNode('alias')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(
                            function ($value) {
                                return preg_match('#^[a-zA-Z0-9_]+$#', $value) === 0;
                            }
                        )
                        ->thenInvalid('Alias should be a string containing letters and underscore. "%s" given')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
