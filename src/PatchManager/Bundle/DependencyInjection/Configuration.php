<?php

namespace Cypress\PatchManager\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('patch_manager');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('handlers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('state_machine')->end()
                    ->end()
                ->end()
                ->scalarNode('dispatch_events')->defaultTrue()->end()
                ->scalarNode('strict_mode')->defaultFalse()->end()
                ->scalarNode('alias')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(fn ($value) => 0 === preg_match('#^[a-zA-Z0-9_]+$#', $value))
                        ->thenInvalid('Alias should be a string containing letters and underscore. "%s" given')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
